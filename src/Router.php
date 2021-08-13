<?php
namespace Nickyeoman\Framework;

/**
* Router Class
* v1.0
* URL: TBA
**/

class Router {

  public $uri = array();
  public $controller = '';
  public $action = '';
  public $params = array();
  public $env = null;

  /**
  * Find the Controller Paths
  **/
  public function __construct() {

    $this->makeUri();
    if ( ! $this->makeController() ) {

      //TODO: this is sloppy, should just be a make here, 404 page should probably be i constructor
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

    //drop the first empty
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

    //Check controller exists
    if ( file_exists( $_ENV['realpath'] . "/" . $_ENV['CONTROLLERPATH'] . '/' . $controller . '.php' ) ) {

      return true;

    } else {

      //404
      return false;

    }

    //TODO: what if action doesn't exist?

    return true;

  } //makeController


} //class
