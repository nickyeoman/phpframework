<?php
namespace Nickyeoman\Framework;

/**
* Router Class
* v2.0
* URL: https://github.com/nickyeoman/phpframework/blob/main/docs/router.md
**/

class Router {

  // Variables *****************************************************************
  // uri in array format [controller,method,param1,parm2] from /controller/method/param1/param2
  private $uri         = array();
  // The controller to call (index is default)
  public $controller  = '';
  // The method to call (index is default, override as fallback)
  public $action      = '';
  // parameters on URL (not includeing post/get)
  public $params      = array();
  // content of contoller file (for method checking)
  private $filecontent = '';

  // Fuctions ******************************************************************

  public function __construct() {

    //creates $this->uri array
    $this->_makeUri();

    // will set this->controller
    $this->_setController();

    // set the action
    if ( empty( $this->action ) )
      $this->_setMethod();

    // Cleanup
    unset($this->filecontent);
    unset($this->uri);

  }

  //Grab the URI and make it a usable array
  private function _makeUri() {

    //remove get (will grab parameters using GET)
    $builduri = strtok($_SERVER['REQUEST_URI'], '?');

    //put into array
    $uriarr = explode("/", $builduri);

    //drop the first as it should be empty
    array_shift($uriarr);

    //make array available
    $this->uri = $uriarr;

  }
  // end makeuri()

  // Set the controller
  private function _setController() {

    /**
    * set controller
    **/
    if ( empty( $this->uri[0] ) ) {

      // we got nothing (just slash)
      $this->controller = 'index';
      $this->action = 'index';
      $this->params = array();

    } else {

      // Controller filename
      $filename = $_ENV['realpath'] . '/' . $_ENV['CONTROLLERPATH'] . '/' . strtolower($uri[0]) . '.php';

      if ( file_exists( $filename ) ) {

        $this->filecontent = file_get_contents($filename); //set variable
        $this->controller = strtolower($this->uri[0]);
        array_shift($this->uri);

      } else {

        //404
        $this->controller = 'error';
        $this->action = '_404';
        $this->params = array();

      }
      // end file exists

    }
    //end empty uri

  }
  // end setController()

  // set the method
  private function _setMethod() {

    // set action/method
    if ( empty( $this->uri[0] ) ) {

      $action = 'index';

    } else { // action is not index or empty

      $action = strtolower($uri[0]);

      // check the file to see if the function exists
      if ( strpos( $this->filecontent, "function $action" ) !== false ) {

        // The function exists
        $this->action = $action;
        array_shift($uri);
        if ( !empty($uri) )
          $this->params = $uri;

      } else { // the action does not exist

        // If override exists there are no methods the second parameter is a variable
        if ( strpos( $this->filecontent, "function override" ) !== false ) {

          $this->action = "override";
          if ( !empty($uri) )
            $this->params = $uri;

        } else {

          //404
          $this->controller = 'error';
          $this->action = '_404';

          bdump("Error: The Method ($action) doesn't exist in the Controller file ($filename)", 'Router Error');

        }

      }
      //end check file

    }
    // end set action/method
  }

} //class
