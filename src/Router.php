<?php
namespace Nickyeoman\Framework;

/**
* Router Class
* v3.0
* URL: https://github.com/nickyeoman/phpframework/blob/main/docs/router.md
**/

class Router {

  // Variables *****************************************************************
  // uri in array format [controller,method,param1,parm2] from /controller/method/param1/param2
  private $uri         = array();

  // The controller to call 
  // TODO: (index is default, but should it be error?)
  public $controller   = '';

  // Controller Class (object)
  public $controllerClass = "";

  // The method to call (index is default, override as fallback)
  public $action       = '';

  // parameters on URL (not includeing post/get)
  public $params       = array();
  
  // content of contoller file (for method checking)
  private $filecontent = '';

  // Debugging
  private $debug = false;
  private $debuglog = array();


  // Fuctions ******************************************************************

  public function __construct() {

    // creates $this->uri array
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

    //remove get
    $builduri = strtok($_SERVER['REQUEST_URI'], '?');
    if ($this->debug) {
      array_push($this->debuglog, "builduri");
      dump($builduri);
    }

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

    // default
    if ( empty( $this->uri[0] ) ) {

      // we got nothing (just slash)
      $this->controller = 'index';
      $this->controllerClass = "Nickyeoman\Framework\Controller\\" . $this->controller;
      $this->action = 'index';
      $this->params = array();

    } else if ($this->uri[0] == "sitemap.xml") {

      //TODO: make some sort of check file for special paths like this
      $this->controller = 'sitemap';
      $this->action = 'sitemap';
      $this->controllerClass = "Nickyeoman\Framework\Components\sitemap\sitemapController"; // Change to framework compoenents

    } else {

      $this->controller = strtolower($this->uri[0]);
      $this->controllerClass = "Nickyeoman\Framework\Controller\\" . $this->controller;

      // Controller filename
      $filename = $_ENV['BASEPATH'] . '/' . $_ENV['CONTROLLERPATH'] . '/' . $this->controller . '.php';

      if ( file_exists( $filename ) ) {

        $this->filecontent = file_get_contents($filename); //set variable
        array_shift($this->uri);

      } else {

        $filename = $_ENV['BASEPATH'] . '/vendor/nickyeoman/phpframework/src/Components/' . $this->controller . '/' . $this->controller . 'Controller.php';
        
        /**
        * You can override this in env to disable all
        * or just create controller of same name for specific components
        **/
        if ( file_exists( $filename ) && $_ENV['USECMS'] == "yes" ) {
    
          $this->filecontent = file_get_contents($filename); //set variable
          $this->controllerClass = "Nickyeoman\Framework\Components\\" . $this->controller . '\\' . $this->controller . 'Controller'; // Change to framework compoenents
          array_shift($this->uri);

        } else {
  
          //404
          $this->controller = 'error';
          $this->action = 'index';
          $this->params = array();
          $this->controllerClass = "Nickyeoman\Framework\Components\\" . $this->controller . '\\' . $this->controller . 'Controller'; // Change to framework compoenents

        }
        // end cms file exists

      }
      // end user controller file exists

    }
    //end empty uri

  }
  // end setController()

  // set the method
  private function _setMethod() {

    // empty so index
    if ( empty( $this->uri[0] ) )
      $this->action = 'index';
    else
      $this->action = strtolower($this->uri[0]);

    // check the file to see if the function exists
    if ( strpos( $this->filecontent, "function $this->action" ) !== false ) {

        // The function exists
        array_shift($this->uri);
        if ( !empty($this->uri) )
          $this->params = $this->uri;

    } elseif ( strpos( $this->filecontent, "function override" ) !== false ) {

      $this->action = "override";

      // TODO: You always need a parameter with an override (controller might handle this though)
      if ( !empty($this->uri[0]) ) {
        $this->params = $this->uri;
      } else {

        $this->controller = 'error';
        $this->action = 'index';
        $_ENV['CONTROLLERPATH'] = "vendor/nickyeoman/phpframework/components/$this->controller/"; //TODO: can't override this

      }

    } else { // action is not index or empty

      //404
      $this->controller = 'error';
      $this->action = 'index';
      $_ENV['CONTROLLERPATH'] = "vendor/nickyeoman/phpframework/components/error/"; //TODO: can't override this

    }
    //end check file

    }
    // end set action/method

} //class
