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
  public $data = array('error' => array(), 'notice' => null);

  /*
  * Base controller manages Sessions, Get, Post and Database
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

  // Add an error
  public function adderror($string = "No Information Supplied", $name = null ) {

    // current size of array
    $count = count($this->data['error']);

    //assign key to array
    if ( empty($name) )
      $name = $count + 1;

    $this->data['error']["$name"] = $string;

    return( count($this->data['error']) );
  }

  // count errors
  public function counterrors() {
    return( count($this->data['error']) );
  }

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

      //debug
      bdump($this->session, "Session Data, Existing");

    }

    $this->data['formkey'] = $this->session['formkey'];
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

      } else {

        //error no form key.
        $this->adderror( 'There was a problem with the session, try again', 'formkey' );

      }

    }
    //end if
  }
  // end setPost()

}
// end BaseController
