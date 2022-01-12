<?php
namespace Nickyeoman\helpers;
USE \Nickyeoman\Validation;

class pageHelper {

  public $page = array();

  /*
  * Make sure there is not a session
  */
  public function __construct($page) {

    //do work
    $this->page = $page;
  }
  // End Construct

  public function insertArray(){
    $array = array(
      'id' => $this->page['id'],
      'title' => $this->page['title'],
      'slug'  => $this->page['slug'],
      'body'  => $this->page['body']
    );
    return $array;
  }


}
