<?php
namespace Nickyeoman\Framework;

/**
* Framework Class (Base Controller and Router)
* v0.1
* Last Updated: Mar 16, 2021
* URL: TBA
*
* Changelog:
* v0 working on 1.0 release
**/

class Framework {

  public $uri = array();
  public $controller = '';
  public $action = '';
  public $params = array();

  /**
  * Find the Controller Paths
  **/
  public function __construct() {

    $this->makeUri();
    $this->makeController();

  }

  /*
  * uses vars to get child controller
  */
  public function loadController() {

    call_user_func_array( array(new $classname(), $r->action), $r->params );

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

    //Check controller exists
    //TODO: need filepath to parameters, see: composer require vlucas/phpdotenv
    if ( file_exists('../Controllers/' . $controller . '.php') ) {

      $this->controller = $controller;
      $this->action = $action;
      $this->params = $uri;

      return true;

    } else {

      //TODO: this is sloppy
      header('HTTP/1.1 404 Not Found');
      echo 'This is 404 page. <a href="/">home</a>';
      exit();
      return false;

    }

    return true;

  } //makeController


} //class
