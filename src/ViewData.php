<?php
namespace Nickyeoman\Framework;

class ViewData {

  public $data = array('error' => array(), 'notice' => null);
  public $session;

  /*
  * Manages the data sent to the view
  */
  public function __construct($session) {

    $this->session = $session;
    
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

  public function set($key, $value) {
    $this->data[$key] = $value;
  }

  // Sets the (clean) post data
  public function setPost($post = array()) {

    $this->data['post'] = $post;

  }

}// end viewData
