<?php
namespace Nickyeoman\Framework;
session_start();

class BaseController {

  public $session = array();
  public $destroy = false;

  /*
  * Manage the session
  */
  public function __construct() {

    //debug
    dump($_SESSION);
    // sessions
    if ( empty($_SESSION['sessionid']) ) {

      $this->session['sessionid'] = session_id();
      //$this->setVar( 'sessionid', session_id() );
      $this->session['formkey'] = md5( session_id(). date("ymdhis") );
      //$this->setVar( 'formkey', md5( session_id(). date("ymdhis") ) );

    } else {

      $this->session = $_SESSION;

    }

  }

  /*
  * place what you have in the session var into session
  */
  public function writeSession() {

    if (!$this->destroy) {
      $_SESSION = $this->session;
    }

  }

  public function setFlash($name, $value){
    $this->session['flash']["$name"] = $value;
  }

  public function readFlash($name) {
    if ( isset( $this->session['flash']["$name"] ) ) {
      $value = $this->session['flash']["$name"];
      unset($this->session['flash']["$name"]);
      return $value;
    } else {
      return false;
    }
  }

  /*
  * This would be logout
  */
  public function destroy() {

    session_destroy();
    $this->destroy = true;

  }

  /*
  * Redirect the page
  */
  function redirect($controller = 'index', $action = 'index') {
    header("Location: /$controller/$action");
    exit;
  }

  /*
  * Call Twig as a view
  */
  public function twig($viewname = 'index', $vars = array() ) {

      //TWIG
      $loader = new \Twig\Loader\FilesystemLoader($_ENV['realpath'] . '/' . $_ENV['VIEWPATH']);
      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['realpath'] . '/' .$_ENV['TWIGCACHE'],
      ]);

      echo $this->twig->render("$viewname.html.twig", $vars);

  }

}
