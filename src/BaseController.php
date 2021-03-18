<?php
namespace Nickyeoman\Framework;

class BaseController {

  public $test = 'hello world';

  public function __construct() {

    echo "started base controller";

  }

  public function twig($viewname = 'index', $vars = array() ) {

    //TODO: ensure correct file/directory path
      $path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
      //TWIG
      $loader = new \Twig\Loader\FilesystemLoader($path . '/Views');
      $this->twig = new \Twig\Environment($loader, [
          //'cache' => $path . '/cache',
      ]);

      echo $this->twig->render("$viewname.html.twig", $vars);

  }

}
