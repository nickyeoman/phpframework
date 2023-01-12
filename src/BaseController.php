<?php
namespace Nickyeoman\Framework;
USE \Nickyeoman\Dbhelper;
USE Nickyeoman\Framework\SessionManager as Session;

// load helpers
$loader = new \Nette\Loaders\RobotLoader; // https://doc.nette.org/en/3.1/robotloader
$loader->addDirectory( $_ENV['BASEPATH'] . "/" . $_ENV['HELPERPATH'] ); //helpers
$loader->setTempDirectory( $_ENV['BASEPATH'] . "/" . $_ENV['LOADERTMPDIR'] ); // use 'temp' directory for cache
$loader->register(); // Run the RobotLoader

class BaseController {

  public $db = null; //class
  public $session = null; //class
  public $post = array('submitted' => false);
  public $data = array('error' => array(), 'notice' => null);

  /*
  * Base controller manages Sessions, Get, Post and Database
  */
  public function __construct() {

    // sessions
    $this->session = new Session();

    $this->populateData();

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

  }
  // End construct

  // sets the data array for view
  public function populateData() {

    // Set all the Global view variables
    $this->data = [
      'uri'     => rtrim(ltrim($_SERVER['REQUEST_URI'], "\/"), "\/")
      ,'pageid' => str_replace("/", "-", rtrim(ltrim($_SERVER['REQUEST_URI'], "\/"), "\/"))
      ,'agent'  => $_SERVER['HTTP_USER_AGENT']
    ];

    if ( empty($_SERVER['HTTP_X_REAL_IP']) )
      $this->data['ip'] = $_SERVER['REMOTE_ADDR'];
    else
      $this->data['ip'] = $_SERVER['HTTP_X_REAL_IP'];

      // Session Data for the view
      $this->data = array_merge($this->data, [
        'formkey'   => $this->session->getKey('formkey')
        ,'loggedin' => $this->session->getKey('loggedin')
      ]);

      // Session flash data for the view
      if ( !empty($this->session->session['flash']) ){
        foreach ( $this->session->session['flash'] as $key => $value ) {

          if ( is_array($value) ) {
            foreach( $value as $k => $v ) {
              if ( !empty($v) )
                $this->adderror($v, $key );
            }
          } else {
            if ( !empty($value) )
              $this->adderror($value, $key );
          }

        }
        $this->session->clearflash();

      }

      //permissions
      if ( $this->session->inGroup('admin') )
        $this->data['admin'] = 'admin';

      bdump($this->data,"View Data");

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

    $this->session->writeSession();

    // php redirect
    exit(header("Location: $controller$action"));

  }
  // END redirect

  //Call Twig as a view
  public function twig($viewname = 'index', $vars = array(), $component = null ) {

      //TWIG
      $loader = new \Twig\Loader\FilesystemLoader($_ENV['BASEPATH'] . '/' . $_ENV['VIEWPATH']);
      $loader->addPath($_ENV['BASEPATH'] . '/vendor/nickyeoman/nytwig/src', 'nytwig');

      //load component view
      if ( $component != null ) {

        $loader->prependPath($_ENV['BASEPATH'] . "/vendor/nickyeoman/phpframework/src/Components/$component/twig");
        
      }
      // end if load component view

      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['BASEPATH'] . '/' .$_ENV['TWIGCACHE'],
          'debug' => true,
      ]);

      $this->twig->addExtension(new \Twig\Extension\DebugExtension());

      echo $this->twig->render("$viewname.html.twig", $vars);
      $this->session->writeSession();
      $this->db->close();

  }
  // End twig

  /**
  * Add an error
  * Errors are an array stored to a session
  * Returns the count of errors
  **/
  public function adderror($string = "No Information Supplied", $name = null ) {

    if ( !isset($this->data['messages']) )
      $this->data['messages'] = array();

    array_push($this->data['messages'], array($name => $string));

    return true;

  }
  // End adderror

  // count errors
  public function counterrors() {

    $count = count($this->data['error']);
    if ( empty($count) )
      $count = 0;
    return( $count );

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

  /**
   * Log to the database
   */
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
        'ip'        => $this->data['ip'],
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

  // Gets the POST and puts it in post
  private function setPost() {

    if ( ! empty( $_POST['formkey'] ) ){

      //check session matches
      if ( $_POST['formkey'] == $this->session->getKey('formkey') ) {

        $this->post['submitted'] = true;

        foreach( $_POST as $key => $value){
          //clean variable TODO: might not want this all the time (html)
          $prep = trim(strip_tags($value));
          $this->post[$key] = htmlentities($prep, ENT_QUOTES);
        }

      } else {

        //error no form key.
        $this->adderror( 'There was a problem with the session, try again', 'error' );

      }

    }
    //end if
    bdump($this->post, 'BC set post');
  }
  // end setPost()

}
// end BaseController
