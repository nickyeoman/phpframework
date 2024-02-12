<?php
namespace Nickyeoman\Framework\Components\error;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;

class errorController {
  
  private $twig;

  public function __construct($container) {
    $this->twig = $container->getTwigRenderer();
  }

  public function index() {

    $s = new SessionManager();
		$v = new ViewData($s);

    //$this->log('error','404', '404 Controller - error.php', '404 page' );
    header('HTTP/1.1 404 Not Found');
    $this->twig->render('404', $v->data, 'error');

  }

}
