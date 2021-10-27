<?php
namespace Nickyeoman\Framework;

/**
* Router Class
* v1.1
* URL: TBA
**/

class Router {

  public $uri = array();
  public $controller = '';
  public $action = '';
  public $params = array();
  public $env = null;

  /**
  * Initiated Begin work
  **/
  public function __construct() {

    $this->makeUri(); //creates $this->uri array

    // 404 error
    if ( ! $this->makeController() ) {

      //TODO: call a twig template
      header('HTTP/1.1 404 Not Found');
      echo 'This is 404 page. <a href="/">home</a>';
      exit();  // must stop here or controller will still be called.

    }

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
  * /controller/action/parms
  * / and /index would equal /index/index
  * /oneThing would equal /oneThing/index
  **/
  private function makeController() {

    $uri = $this->uri;

    // case /
    if ( empty( $uri[0] ) ) {

      $controller = 'index';

    } else {

      $controller = strtolower($this->uri[0]);
      array_shift($uri);

    }

    //get action
    if ( empty( $uri[0] ) ) {

      $action = 'index';

    } else {

      $action = strtolower($uri[0]);
      array_shift($uri);

    }

    //set parms even with error
    $this->controller = $controller;
    $this->action = $action;
    $this->params = $uri;

    // Now begin error checking
    // Check controller exists
    $filename = $_ENV['realpath'] . "/" . $_ENV['CONTROLLERPATH'] . '/' . $controller . '.php' )
    if ( file_exists( $filename ) {

      return true;

    } else {

      //404
      dump("Error: The Controller file ($filename) doesn't exist");
      return false;

    }

    // Check if action exists
    $filecontent = file_get_contents($filename);

    if ( strpos( $filecontent, "function $action" ) !== false ) {

      // The method exists
      return true;

    } else {

      // If override exists there are no methods the second parameter is a variable
      if ( strpos( $filecontent, "function override" ) !== false ) {

        return true;

      } else {

        dump("Error: The Method ($action) doesn't exist in the Controller file ($filename)");
        return false;

      }

    }
    //End check if action exists

    dump("Error: you shouldn't get through the Router.");

  } //makeController


} //class
