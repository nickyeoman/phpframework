<?php namespace Nickyeoman\Framework;
/**
* Session Class
* v1.2
**/

class SessionManager {

  // Variables *****************************************************************

  public $data = array();
  public $debugEnabled = false;
  private $debug_log = array();

  // Construct ******************************************************************

  public function __construct() {

    //Start creating the session array
    if ( empty( $_SESSION['sessionid'] ) )
      $this->newSession(); //Create the variables
    else
      $this->existingSession(); // Put SESSION into our array

    $this->data['page'] = strtok($_SERVER['REQUEST_URI'], '?');

  }
  // end construct

  // Debugging ******************************************************************
  public function debug($bool = true ) {

    $this->debugEnabled = $bool;
    array_push($this->debug_log,'# Enabled Debug Mode');
    array_push($this->debug_log,'## In Session');
    array_push($this->debug_log,$_SESSION);
    array_push($this->debug_log,'## In data');
    array_push($this->debug_log,$this->data);

  }

  public function showDebug() {
    dump($this->debug_log);
  }

  // Functions ******************************************************************
  public function newSession() {

    // Empty session, create a new one
    $this->data = array(
      'sessionid' => session_id()
      ,'formkey'  => md5( session_id() . date("ymdhis") ) //xss
      ,'loggedin' => false
      ,'user'     => array()
      ,'ugroups'  => array()
      ,'flash'    => array()
      ,'page'     => strtok($_SERVER['REQUEST_URI'], '?')
      ,'pageid'   => null //this is the last pageid set, if you visit other pages that don't have an id, the one with an id will remain
    );

    $this->writeSession();

    //debug
    if ($this->debugEnabled) {
      array_push($this->debug_log,'# New Session');
      array_push($this->debug_log,$this->data);
    }

  }

  public function existingSession() {

    // Populate our array with existing session
    $this->data = $_SESSION;

  }

  // Write the current session array to PHP session
  public function writeSession() {

      $_SESSION = $this->data;

      //debug
      if ($this->debugEnabled) {
        array_push($this->debug_log,'# Session written');
        array_push($this->debug_log,$this->data);
      } 

  }
  // End writeSession

  // Destroy session (logout mostly)
  public function destroySession() {

    session_destroy();
    unset($this->data);
    $this->newSession();
    return true;

  }

  /**
   * Flash is an array in sessions that gets read and cleared after each page load.
   */
  public function addflash($string = "Error, no message",$name = null) {

    if ( empty($string) )
      return false;

    if ( array_key_exists($name, $this->data['flash'] ) )
      array_push($this->data['flash'][$name], $string);
    else
      $this->data['flash'][$name] = array($string);

    return true;

  }

  public function clearflash(){

    unset($this->data['flash']);
    $this->data['flash'] = array();
    return true;

  }

  /**
   * How many flash messages are there?
   */
  public function flashcount($what = 'all'){

    if ( $what = 'all' )
      return count($this->data['flash']);
    else
      return count($this->data['flash'][$what]);

  }

  /**
   * Get the value of a session key
   */
  public function getKey($key = '') {

    if ( array_key_exists( $key, $this->data ) ) {

      //debug
      if ($this->debugEnabled) {
        array_push($this->debug_log,'# Got Key');
        array_push($this->debug_log,$key);
        array_push($this->debug_log,$this->data[$key]);
      }

      return $this->data[$key];

    } else {
      if ($this->debugEnabled) {
        array_push($this->debug_log,"# Got Key ($key), but none found");
      }
      return false;

    }

  }

  /**
   * Set data to the session
   */
  public function setKey( $key = null, $value = null ) {

    if ( !empty($key) && !empty($value) )
      $this->data["$key"] = $value;
    else
      return false;

    return true;

  }

  public function setUserGroups($groups = array()) {
    $this->data['ugroups'] = $groups;
    return true;
  }

   /**
   * Session Loggedin
   * Checks the session for loggedin status
   * If false updates the session and returns false
   */
  public function loggedin($flashmsg = '', $writeS = false) {

    if ( $this->data['loggedin'] ) {

      return true;

    } else {

      $this->addflash($flashmsg, 'error');

      if ($writeS)
        $this->writeSession();

      return false;

    }

  }

  public function authorize($userdata = array() ){
    $this->data['loggedin'] = true;
    $this->data['user'] = $userdata;
    $this->writeSession();
    return true;
  }

  public function isAdmin(){

    //debug
    if ($this->debugEnabled) {
      array_push($this->debug_log,'# IsAdmin Called');
      array_push($this->debug_log,$this->data['ugroups']);
      
    }

    if ( array_search('admin', $this->data['ugroups']) !== NULL ) 
      return true;
    else
      return false;
    
  }

  public function inGroup($needle) {

    //debug
    if ($this->debugEnabled) {
      array_push($this->debug_log,'# InGroup Called ' . $needle);
      array_push($this->debug_log,$this->data['ugroups']);
    }

    if ( array_search($needle, $this->data['ugroups']) !== NULL ) 
      return $needle;
    else
      return false;

  }

} //class