<?php
require_once $_ENV['BASEPATH'] . '/vendor/autoload.php'; // Composer

// dotenv
$dotenv = Dotenv\Dotenv::createImmutable($_ENV['BASEPATH']); // Grab dotenv https://github.com/vlucas/phpdotenv
$dotenv->load();

// Tracy debugger
USE Tracy\Debugger; // https://tracy.nette.org/
if ( $_ENV['DEBUG'] == 'display' )
  Debugger::enable(Debugger::DEVELOPMENT);

// Route
USE Nickyeoman\Framework\Router as Router;
$router = new Router();
$theController = new $router->controllerClass();
call_user_func_array( array($theController, $router->action), [$router->params] );