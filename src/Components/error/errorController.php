<?php
namespace Nickyeoman\Framework\Components\error;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;

class errorController extends \Nickyeoman\Framework\BaseController {

  public function index() {

    $s = new SessionManager();
		$v = new ViewData($s);

    $this->log('error','404', '404 Controller - error.php', '404 page' );
    header('HTTP/1.1 404 Not Found');
    $this->twig('404', $v->data, 'error');

  }

}
