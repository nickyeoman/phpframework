<?php
namespace Nickyeoman\Framework;
USE \Nickyeoman\Dbhelper;
session_start();

// load helpers
$loader = new \Nette\Loaders\RobotLoader; // https://doc.nette.org/en/3.1/robotloader
$loader->addDirectory( $_ENV['realpath'] . "/" . $_ENV['HELPERPATH'] ); //helpers
$loader->setTempDirectory( $_ENV['realpath'] . "/" . $_ENV['LOADERTMPDIR'] ); // use 'temp' directory for cache
$loader->register(); // Run the RobotLoader

class BaseController {

  public $session = array();
  public $destroy = false;
  public $loggedin = 0; //0 or 1
  public $db = null;
  public $post = array('submitted' => false);
  public $data = array('error' => array(), 'notice' => null);

  /*
  * Base controller manages Sessions, Get, Post and Database
  */
  public function __construct() {

    // Set all the Global view variables
    $this->data = array_merge($this->data, [
      'uri'     => rtrim(ltrim($_SERVER['REQUEST_URI'], "\/"), "\/")
      ,'pageid' => str_replace("/", "-", rtrim(ltrim($_SERVER['REQUEST_URI'], "\/"), "\/"))
      ,'ip'     => $_SERVER['REMOTE_ADDR']
      ,'agent'  => $_SERVER['HTTP_X_REAL_IP']
    ]);

    // sessions
    $this->setSession();

    // Store the database object to a variable in this object
    if ( ! empty( $_ENV['DBUSER'] ) )
      $this->db = new \Nickyeoman\Dbhelper\Dbhelp(
        $_ENV['DBHOST']
        , $_ENV['DBUSER']
        , $_ENV['DBPASSWORD']
        , $_ENV['DB']
        , $_ENV['DBPORT']
      );

    // POST
    $this->setPost();

    // Admin
    if ( $this->session['loggedin'] )
      $this->data['admin'] = 1;
    else
      $this->data['admin'] = 0;

  }
  // End construct

  // Write the current session to PHP session
  public function writeSession() {

    if ( ! $this->destroy )
      $_SESSION = $this->session;
    else
      session_destroy();

  }
  // End writeSession

  // Destroy session (logout mostly)
  public function destroySession() {

    session_destroy();
    $this->destroy = true;

  }

  /**
  * Redirect to the correct controller and action (page)
  **/
  public function redirect($controller = 'index', $action = 'index') {

    // set controller
    if ($controller == 'index' || empty($controller) )
      $controller = '/';
    else
      $controller = "/$controller";

    // set method/action
    if ($action == 'index' || empty($action) )
      $action = '';
    else
      $action = "/$action";

    // php redirect
    header("Location: $controller$action");
    exit(); // do not execute anything more

  }
  // END redirect

  //Call Twig as a view
  public function twig($viewname = 'index', $vars = array() ) {

      //TWIG
      $loader     = new \Twig\Loader\FilesystemLoader($_ENV['realpath'] . '/' . $_ENV['VIEWPATH']);

      //load component view
      if ( str_contains($_ENV['CONTROLLERPATH'], 'vendor') ) {

        $arr = explode('/' , $_ENV['CONTROLLERPATH']);
        $component = $arr[4];
        $loader->prependPath($_ENV['realpath'] . "/vendor/nickyeoman/phpframework/components/$component/twig");

      }
      // end if load component view

      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['realpath'] . '/' .$_ENV['TWIGCACHE'],
          'debug' => true,
      ]);

      $this->twig->addExtension(new \Twig\Extension\DebugExtension());

      echo $this->twig->render("$viewname.html.twig", $vars);
      $this->writeSession();
      $this->db->close();

  }
  // End twig

  /**
  * Add an error
  * Errors are an array stored to a session
  * Returns the count of errors
  **/
  public function adderror($string = "No Information Supplied", $name = null ) {

    // current size of array
    $count = count($this->data['error']);

    //assign key to array
    if ( empty($name) )
      $name = $count + 1; // TODO: I don't think this is right

    $this->data['error']["$name"] = $string;

    return( count($this->data['error']) );

  }
  // End adderror

  // count errors
  //TODO: the above function adderror does the same with no parameters, merge them
  public function counterrors() {
    return( count($this->data['error']) );
  }

  /**
  * A wrapper for the nette SMTP
  * make sure your ENV is set
  * won't send in debug mode
  * easy to use $this->sendEmail($to,$subject,$body);
  **/
  public function sendEmail($to = '', $subject = '', $body = '') {

    // Check params
    if ( empty($to) || empty($subject) || empty($body) )
      dump('All params required: $this->sendEmail($to,$subject, $body)');
      return false;

    // Check env
    if ($_ENV['DEBUG'] != 'display'){

      // https://packagist.org/packages/nette/mail
      $mail = new \Nette\Mail\Message;

      $mail->setFrom($_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] .'>')
        ->addTo( $to )
        ->setSubject( $subject )
        ->setBody( $body )
      ;

      $mailer = new \Nette\Mail\SmtpMailer([
         'host'     => $_ENV['MAIL_HOST'],
         'username' => $_ENV['MAIL_USERNAME'],
         'password' => $_ENV['MAIL_PASSWORD'],
         'secure'   => $_ENV['MAIL_ENCRYPTION'],
         'port'     => $_ENV['MAIL_PORT'],
       ]);

      $mailer->send($mail);
    }
    // end if debug

  }
  // end sendEmail

  /**
  * Grabs a markdown file
  * Parm1: Path to file from index.php
  * Parm2: key from array to populate default: $this->data['content']
  **/
  public function markdownFile($filename = '', $datakey = 'content') {
    if ( empty($filename) ) {
      die("Error: no filename given to mardownFile");
    }

    $f = fopen($filename, 'r');

    if ($f) {
     $contents = fread($f, filesize($filename));
     fclose($f);

     $Parsedown = new \Parsedown();
     $this->data[$datakey] = $Parsedown->text( $contents );
     return $contents;
   } else {
     die("Error: mardownFile, file not found");
   }

  }
  //end markdownFile

  // A little bit of logging
  public function log($level = 'DEBUG', $title = 'Called log', $location = 'Base Controller', $content = "NULL") {
    if ($_ENV['LOGGING'] == 'mysql') {

      //prepare post
      if ( $this->post['submitted'] ) {
        unset($this->post['password']);
        $post = json_encode($this->post);
      } else {
        $post = null;
      }

      $log = array(
        'level'     => strtoupper($level),
        'title'     => $title,
        'content'   => $content,
        'location'  => $location, //location of code
        'ip'        => $_SERVER['HTTP_X_REAL_IP'],
        'url'       => $this->data['uri'],
        'session'   => json_encode($this->session),
        'post'      => $post,
        'time'      => 'NOW()'
      );

      foreach ($log as $key => $value) {
        if ( empty($value) ) {
          unset($log[$key]);
        }
      }

      $this->db->create('logs', $log);
    }
    // end mysql
  }
  // end function log

  // Takes the SESSION and places it in session
  private function setSession(){

    if ( empty( $_SESSION['sessionid'] ) ) {

      // Empty session, create a new one
      $this->session['sessionid'] = session_id();
      $this->session['formkey']   = md5( session_id() . date("ymdhis") );
      $this->session['loggedin']  = 0;
      $this->session['notice'] = '';
      $this->session['error'] = '';
      $this->writeSession();
      bdump($this->session, "Session Data, New");

    } else {

      $this->session = $_SESSION; // Store the session to this object in an array

      //flash data
      if ( !empty( $this->session['notice'] ) ) {
        $this->data['notice'] = $this->session['notice'];
        unset($this->session['notice']);
      }
      if ( !empty( $this->session['alert']  )) {
        $this->data['alert'] = $this->session['alert'];
        unset( $this->session['alert']);
      }

      if ( isset($this->session['NY_FRAMEWORK_USER']) ) {
        if ( $this->session['NY_FRAMEWORK_USER']['loggedin'] )
          $this->session['loggedin'] = 1;
      }

      //debug
      bdump($this->session, "Session Data, Existing");

    }

    // Session Data for the view
    $this->data = array_merge($this->data, [
      'formkey'   => $this->session['formkey']
      ,'loggedin' => $this->session['loggedin']
    ]);

  }
  // end setSession()

  // Gets the POST and puts it in post
  private function setPost() {

    if ( ! empty( $_POST['formkey'] ) ){

      //check session matches
      if ( $_POST['formkey'] == $this->session['formkey'] ) {

        $this->post['submitted'] = true;

        foreach( $_POST as $key => $value){
          //clean variable TODO: might not want this all the time (html)
          $prep = trim(strip_tags($value));
          $this->post[$key] = htmlentities($prep, ENT_QUOTES);
        }

      } else {

        //error no form key.
        $this->adderror( 'There was a problem with the session, try again', 'formkey' );

      }

    }
    //end if
    bdump($this->post);
  }
  // end setPost()

}
// end BaseController
