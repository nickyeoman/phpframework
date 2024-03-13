<?php namespace Nickyeoman\Framework\Classes;

class paginationHelper {

  public $num_articles = 0;
  public $num_pages = 1;
  public $current_page = 1;
  public $offset = 0;
  public $limit = 5;
  public $outOfBounds = false;
  public $sql_limit = "0,1"; // no prepended LIMIT
  public $data = array(); // for view


  /*
  * We expect data to be clean before getting here
  */
  public function __construct($limit = 5, $current_page = 1, $num_articles = 0) {

    // set parameters to object
    $this->limit = $limit;
    $this->current_page = $current_page;
    $this->num_articles = $num_articles;

    // set to object
    $this->num_pages = ceil( $this->num_articles / $this->limit );

    // check bounds
    if ($current_page > $this->num_pages) {
      $this->current_page = $this->num_pages;
      $this->outOfBounds = true;
    }

    // set to object
    $this->offset = ($this->current_page-1) * $limit;
    if ($this->offset < 0)
      $this->offset = $this->limit;

    $this->sql_limit = "$this->offset, $this->limit";

    $this->data = array(
      'num_pages' => $this->num_pages
      ,'current_page' => $this->current_page
    );

  }
  // end construct



}
