<?php
namespace Nickyeoman\Framework\Components\error;

use Nickyeoman\Framework\Classes\BaseController;

class errorController extends BaseController {
  
  public function index() {

    //$this->log('error','404', '404 Controller - error.php', '404 page' );
    // Render the template
    $this->view('@error/404');

  }

}
