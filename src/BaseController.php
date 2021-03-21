<?php
namespace Nickyeoman\Framework;

class BaseController {

  public function __construct() {

    //code here

  }

  public function twig($viewname = 'index', $vars = array() ) {

      //TWIG
      $loader = new \Twig\Loader\FilesystemLoader($_ENV['VIEWS']);
      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['TWIGCACHE'],
      ]);

      echo $this->twig->render("$viewname.html.twig", $vars);

  }

}
