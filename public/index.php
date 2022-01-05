<?php
/*******************************************************************************
* Nick Yeoman's php Framework
* https://github.com/nickyeoman/phpframework
*******************************************************************************/
require_once '../vendor/autoload.php'; // Composer

/*******************************************************************************
* Variables
*******************************************************************************/
// Set Env Variables
$_ENV['realpath'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

// Grab Env Variables from file using dotenv https://github.com/vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable($_ENV['realpath']);
$dotenv->load();

/*******************************************************************************
* Debugging
*******************************************************************************/
// Check we are in debug mode "display" will show bar no matter what
USE Tracy\Debugger; // https://tracy.nette.org/
if ( $_ENV['DEBUG'] == 'display' )
  Debugger::enable(Debugger::DEVELOPMENT);

/*******************************************************************************
* Framework autoloader
* (Load the app contents such as Controllers)
*******************************************************************************/
$loader = new Nette\Loaders\RobotLoader; // https://doc.nette.org/en/3.1/robotloader
$loader->addDirectory( $_ENV['realpath'] . "/" . $_ENV['CONTROLLERPATH'] );
$loader->setTempDirectory( $_ENV['realpath'] . "/" . $_ENV['LOADERTMPDIR'] ); // use 'temp' directory for cache
$loader->register(); // Run the RobotLoader

//Use The namespace for the framework in composer
USE Nickyeoman\Framework;

/*******************************************************************************
* Routing
*******************************************************************************/
$router = new Nickyeoman\Framework\Router();
$classname = $router->controller . 'Controller'; //prep the class name for call user func array
call_user_func_array( array(new $classname(), $router->action), [$router->params] ); //Load the child controller
