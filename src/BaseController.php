<?php
namespace Nickyeoman\Framework;
USE \Nickyeoman\Dbhelper;
session_start();

class BaseController {

  public $session = array();
  public $destroy = false;
  public $loggedin = 0;
  public $db = null;
  public $post = array('submitted' => false);

  /*
  * Sessions are managed with the Base Controller
  */
  public function __construct() {

    // sessions
    $this->setSession();

    // Store the database object to a variable in this object
    if ( ! empty( $_ENV['DBUSER'] ) )
      $this->db = new \Nickyeoman\Dbhelper\Dbhelp($_ENV['DBHOST'], $_ENV['DBUSER'], $_ENV['DBPASSWORD'], $_ENV['DB'], $_ENV['DBPORT'] );

    // POST
    $this->setPost();

  }

  // Write the current session to PHP session
  public function writeSession() {

    if ( ! $this->destroy )
      $_SESSION = $this->session;
    else
      session_destroy();

  }

  // Set a flash varaiable (only good until next page load)
  public function setFlash($name, $value){

    $this->session['flash']["$name"] = $value;

  } //end setFlash

  // Get a flash variable
  public function readFlash($name) {

    if ( isset( $this->session['flash']["$name"] ) ) {

      $value = $this->session['flash']["$name"];

      unset($this->session['flash']["$name"]);

      return $value;

    } else {

      return false;

    }

  } //end readFlash

  // Destroy session (logout mostly)
  public function destroySession() {

    session_destroy();
    $this->destroy = true;

  }

  // Redirect to the correct controller and action (page)
  function redirect($controller = 'index', $action = 'index') {

    if ($controller == 'index' || empty($controller) ) {
      $controller = '/';
    } else {
      $controller = "/$controller";
    }

    if ($action == 'index' || empty($action) ) {
      $action = '';
    } else {
      $action = "/$action";
    }

    header("Location: $controller$action");
    exit();

  }

  //Call Twig as a view
  public function twig($viewname = 'index', $vars = array() ) {

      //TWIG
      $loader     = new \Twig\Loader\FilesystemLoader($_ENV['realpath'] . '/' . $_ENV['VIEWPATH']);
      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['realpath'] . '/' .$_ENV['TWIGCACHE'],
          'debug' => true,
      ]);
      $this->twig->addExtension(new \Twig\Extension\DebugExtension());

      // loggedin is meant for the template
      $vars['loggedin'] = $this->session['NY_FRAMEWORK_USER']['loggedin'];

      echo $this->twig->render("$viewname.html.twig", $vars);

  }

  private function setSession(){

    if ( empty( $_SESSION['sessionid'] ) ) {

      // Empty session, create a new one
      $this->session['sessionid'] = session_id();
      $this->session['formkey']   = md5( session_id() . date("ymdhis") );
      $this->session['NY_FRAMEWORK_USER'] = array('loggedin' => 0);
      $this->writeSession();

    } else {

      $this->session = $_SESSION; // Store the session to this object in an array
      bdump($this->session);

    }
    //end if

  }
  // end setSession()

  private function setPost() {

    if ( ! empty( $_POST['formkey'] ) ){

      //check session matches
      if ( $_POST['formkey'] == $this->session['formkey'] ) {

        $this->post['submitted'] = true;

        foreach( $_POST as $key => $value){
          $prep = trim(strip_tags($value));
          $this->post[$key] = htmlentities($prep, ENT_QUOTES);
        }

        //debug
        bdump($this->post);

      } else {

        //error no form key.
        $this->post['error'] = "There was a problem with the session, try again";

      }

    }
    //end if
  }
  // end setPost()

}
// end BaseController
