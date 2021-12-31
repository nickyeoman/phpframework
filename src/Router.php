<?php
namespace Nickyeoman\Framework;

/**
* Router Class
* v1.2
* URL: https://github.com/nickyeoman/phpframework/blob/main/docs/router.md
**/

class Router {

  public $uri = array(); //an array of url segments with controller and action popped off
  public $controller = '';
  public $action = '';
  public $params = array();
  public $env = null;

  /**
  * Initiated Begin work
  **/
  public function __construct() {

    $this->makeUri(); //creates $this->uri array

    // Assign the controller and function(action)
    $this->sorturi();

  }

  /**
  * Grab the URI and make it a usable array
  **/
  private function makeUri() {

    //remove get (will grab parameters using GET)
    $builduri = strtok($_SERVER['REQUEST_URI'], '?');

    //put into array
    $uriarr = explode("/", $builduri);

    //drop the first as it should be empty
    array_shift($uriarr);

    //make array available
    $this->uri = $uriarr;

  }

  /**
  * Which controller and function do we load?
  *
  * We are going to handle the url's as follows so we can autoload Controllers (similar to codeigniter)
  * /controller/action/params
  * / and /index would equal /index/index
  * /oneThing would equal /oneThing/index
  **/
  private function sorturi() {

    $uri = $this->uri;

    /**
    * set controller
    **/
    if ( empty( $uri[0] ) ) {

      // we got nothing (just slash)
      $this->controller = 'index';
      $this->action = 'index';
      $this->params = array();
      return true;

    } else {

      // Controller filename
      $filename = $_ENV['realpath'] . '/' . $_ENV['CONTROLLERPATH'] . '/' . strtolower($uri[0]) . '.php';

      if ( file_exists( $filename ) ) {

        $filecontent = file_get_contents($filename); //set variable
        $this->controller = strtolower($uri[0]);
        array_shift($uri);

      } else {

        //404
        $this->controller = 'error';
        $this->action = '_404';
        $this->params = array();
        $filecontent = null;

      }

    }
    // end controller

    /**
    * set action/method
    **/
    if ( empty( $uri[0] ) ) {

      $this->action = 'index';
      $this->params = $uri; // case: /page/index/param1/param2 (if no override is used)
      return true;

    } else { // action is not index or empty

      $action = strtolower($uri[0]);

      // check the file to see if the function exists
      if ( strpos( $filecontent, "function $action" ) !== false ) {

        // The function exists
        $this->action = $action;
        array_shift($uri);
        $this->params = $uri;
        return true;

      } else { // the action does not exist

        // If override exists there are no methods the second parameter is a variable
        if ( strpos( $filecontent, "function override" ) !== false ) {

          $this->action = "override";
          $this->params = $uri;
          return true;

        } else {

          //404
          $this->controller = 'error';
          $this->action = '_404';
          $this->params = array();
          $filecontent = null;
          bdump("Error: The Method ($action) doesn't exist in the Controller file ($filename)");
          return false;

        }

      }
      //end check file

    }
    // end set action/method


} //sorturi


} //class
