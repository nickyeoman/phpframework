<?php
namespace Nickyeoman\Framework;
USE \Nickyeoman\Dbhelper;
session_start();

class BaseController {

  public $session = array();
  public $destroy = false;
  public $loggedin = 0;
  public $db = null;
  public $post = array(['submitted'] = false;);

  /*
  * Sessions are managed with the Base Controller
  */
  public function __construct() {

    // sessions
    if ( empty( $_SESSION['sessionid'] ) ) {

      // Empty session, create a new one
      $this->session['sessionid'] = session_id();
      $this->session['formkey']   = md5( session_id() . date("ymdhis") );

    } else {

      $this->session = $_SESSION; // Store the session to this object in an array

      if ( !empty( $this->session['loggedin'] ) ) {

        if ( $this->session['loggedin'] == 1 )
          $this->loggedin = 1;

      }

    }

    // Store the database object to a variable in this object
    if ( ! empty( $_ENV['DBUSER'] ) )
      $this->db = new \Nickyeoman\Dbhelper\Dbhelp($_ENV['DBHOST'], $_ENV['DBUSER'], $_ENV['DBPASSWORD'], $_ENV['DB'], $_ENV['DBPORT'] );

    // POST
    if ( ! empty( $_POST['formkey'] ) ){
      //check session matches
      if ( $_POST['formkey'] == $this->session['formkey'] ) {

        $this->post['submitted'] = true;
        foreach( $_POST as $key => $value){
          $this->post[$key] = trim(strip_tags($value));
        }

      } else {

        //error no form key.
        $this->post['error'] = "There was a problem with the session, try again";

      }
    }

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

    header("Location: /$controller/$action");
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

      echo $this->twig->render("$viewname.html.twig", $vars);

  }

}
