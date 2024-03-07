<?php
namespace Nickyeoman\Framework\Controllers;

use Nickyeoman\Framework\Classes\BaseController;

class error extends BaseController {
  
  public function index() {

    header("HTTP/1.0 404 Not Found");
    //$this->log('error','404', '404 Controller - error.php', '404 page' );
    // Render the template
    $this->view('@cms/error/404');

  }

}
