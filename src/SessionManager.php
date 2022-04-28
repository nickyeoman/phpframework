<?php
namespace Nickyeoman\Framework;
session_start();
/**
* Session Class
* v1.0
**/

class SessionManager {

  // Variables *****************************************************************

  public $session = array();

  // Fuctions ******************************************************************

  public function __construct() {

    //Start creating the session array
    if ( empty( $_SESSION['sessionid'] ) )
      $this->newSession(); //Create the variables
    else
      $this->existingSession(); // Put SESSION into our array


    $this->session['page'] = strtok($_SERVER['REQUEST_URI'], '?');

  }

  public function newSession() {

    // Empty session, create a new one
    $this->session['sessionid'] = session_id();
    $this->session['formkey']   = md5( session_id() . date("ymdhis") ); //xss
    $this->session['loggedin']  = false;
    $this->session['usrgrps'] = array();
    $this->session['flash'] = array();
    $this->session['page'] = strtok($_SERVER['REQUEST_URI'], '?');
    $this->session['pageid'] = null; //this is the last pageid set, if you visit other pages that don't have an id, the one with an id will remain
    $this->writeSession();
    bdump($this->session, "New Session");

  }

  public function existingSession() {

    // Populate our array with existing session
    $this->session = $_SESSION;

  }

  public function inGroup($group = '', $msg = '', $writeS = false) {

    $group = trim(strtolower($group));

    if ( empty($group) )
      return false;

    $adminGroups = explode(',',$this->session['admin']);

    if ( is_int(array_search($group, $adminGroups, true ) ) ) {
      return true;
    } else {

      if ( ! empty($msg) )
        $this->session->addflash($msg,'error');

      if ($writeS)
        $this->writeSession();

      return false;
    }
  }

  // Write the current session array to PHP session
  public function writeSession() {

      $_SESSION = $this->session;
      bdump($_SESSION, "Session Written");

  }
  // End writeSession

  // Destroy session (logout mostly)
  public function destroySession() {

    session_destroy();
    unset($this->session);

  }

  public function addflash($string = "Error, no message",$name = null) {

    if ( empty($string) )
      return false;

    if ( array_key_exists($name, $this->session['flash'] ) )
      array_push($this->session['flash'][$name], $string);
    else
      $this->session['flash'][$name] = array($string);

    return true;

  }

  public function clearflash(){

    unset($this->session['flash']);
    $this->session['flash'] = array();
    return true;

  }

  public function flashcount($what = 'all'){

    if ( $what = 'all' )
      return count($this->session['flash']);
    else
      return count($this->session['flash'][$what]);

  }

  // returns the vaule of a session key
  public function getKey($key = '') {

    if ( array_key_exists( $key, $this->session ) )
      return $this->session["$key"];
    else
      return false;

  }

  // returns the vaule of a session key
  public function setKey( $key = null, $value = null ) {

    if ( !empty($key) && !empty($value) )
      $this->session["$key"] = $value;
    else
      return false;

    return true;

  }

  public function dump() {

    bdump($this->session);

  }

  public function loggedin($flashmsg = '', $writeS = false) {
    
    if ( $this->session['loggedin'] ) {
      return true;
    } else {
      $this->addflash($flashmsg, 'error');
      if ($writeS)
        $this->writeSession();

      return false;
    }

  }

} //class
