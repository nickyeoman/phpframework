<?php
// Nick Yeoman's php Framework
require_once $_ENV['BASEPATH'] . '/vendor/autoload.php'; // Composer

// Grab Env Variables from file using dotenv https://github.com/vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable($_ENV['BASEPATH']);
$dotenv->load();

// Check we are in debug mode "display" will show bar no matter what
USE Tracy\Debugger; // https://tracy.nette.org/
if ( $_ENV['DEBUG'] == 'display' )
  Debugger::enable(Debugger::DEVELOPMENT);

//Use The namespace for the framework in composer
USE Nickyeoman\Framework\Router as Router;

//create the class for the router
$router = new Router();

// Require framework (autoload controllers)
$loader = new Nette\Loaders\RobotLoader; // https://doc.nette.org/en/3.1/robotloader
$loader->addDirectory( $_ENV['BASEPATH'] . "/" . $_ENV['CONTROLLERPATH'] );
$loader->setTempDirectory( $_ENV['BASEPATH'] . "/" . $_ENV['LOADERTMPDIR'] ); // use 'tmp' directory for cache
$loader->register(); // Run the RobotLoader

//Load the child controller
call_user_func_array( array(new $router->controllerClass(), $router->action), [$router->params] );