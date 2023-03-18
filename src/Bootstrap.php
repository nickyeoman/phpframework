<?php
require $_ENV['BASEPATH'] . '/vendor/autoload.php'; // Composer

use Dotenv\Dotenv;
use Nickyeoman\Framework\Router;
use Tracy\Debugger;

// dotenv
$dotenv = Dotenv::createImmutable($_ENV['BASEPATH']); // Grab dotenv https://github.com/vlucas/phpdotenv
$dotenv->load();

// Tracy debugger
if ( $_ENV['DEBUG'] == 'display' )
  Debugger::enable(Debugger::DEVELOPMENT);

// Route
$router = new Router();

$controller = new $router->controllerClass();
try {
  call_user_func_array( array($controller, $router->action), [$router->params] );
} catch (Exception $e) {
  echo "An error occurred: " . $e->getMessage();
}