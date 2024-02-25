<?php
namespace Nickyeoman\Framework\Classes;

class ViewData {

  public $data = array('error' => array(), 'notice' => null);
  public $session;

  /*
  * Manages the data sent to the view
  */
  public function __construct(SessionManager $sessionManager) {

    $this->session = $sessionManager;
    
    $this->populateData();

  } // End construct

  // sets the data array for view
  private function populateData() {

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
        ,'admin'    => $this->session->inGroup('admin')
      ]);
      
      // Session flash data for the view
      if ( !empty($this->session->data['flash']) ){
        foreach ( $this->session->data['flash'] as $key => $value ) {

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

      bdump($this->data,"View Data");

  }

  /**
  * Add an error
  **/
  public function adderror($string = "No Information Supplied") {

    $this->data['error'][] = $string;

    return true;

  } // End adderror

  // Add an notice
  public function addnotice($string = "No Information Supplied") {

    $this->data['notice'][] = $string;

    return true;

  } // End adderror

  // count errors
  public function counterrors() {

    if ( empty($this->data['error']) )
      $count = 0;
    else
      $count = count($this->data['error']);

    return( $count );

  }

  /**
   * Add a debug message with support for nested sections
   *
   * @param string $section Section name
   * @param string $title Title of the debug message
   * @param string $string Content of the debug message
   * @return bool Returns true if the debug message is added successfully
   */
  public function debugMsg($section = 'General', $title = 'Expand', $string = "No Information Supplied") {

    if ($_ENV['DEBUG'] != 'display') {
      return false;
    }

    if (!isset($this->data['debug'][$section])) {
        $this->data['debug'][$section] = [];
    }

    $this->data['debug'][$section][] = ['title' => $title, 'string' => $string];

    return true;
  }

  public function debugDump($section = 'General', $title = 'Dump', $var = null) {
    if ($_ENV['DEBUG'] != 'display') {
      return false;
    }

    if (!isset($this->data['debug'][$section])) {
      $this->data['debug'][$section] = [];
    }

    ob_start();
    dump($var);
    $mydump = ob_get_clean();
    
    $this->data['debug'][$section][] = ['title' => $title, 'string' => $mydump];
    return true;

  }

  public function set($key, $value) {
    $this->data[$key] = $value;
  }

  // Sets the (clean) post data
  public function setPost($post = array()) {

    $this->data['post'] = $post;

  }

}// end viewData
