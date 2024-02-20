<?php
namespace Nickyeoman\Framework\Components\error;

use Nickyeoman\Framework\Classes\BaseController;

class errorController extends BaseController {
  
  public function index() {

    //$s = new SessionManager();
		//$v = new ViewData($s);

    //$this->log('error','404', '404 Controller - error.php', '404 page' );
    header('HTTP/1.1 404 Not Found');
    
    // Render the template
    $this->view('@error/404.html.twig', ['one' => 'theone']);


  }

}
