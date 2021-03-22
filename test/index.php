<?php
//Require composer
require_once '../vendor/autoload.php';

// Tracy Debugger https://tracy.nette.org/
use Tracy\Debugger;
Debugger::enable();

//Grab EnvVariables, then prepare them
$realpath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
$_ENV['realpath'] = $realpath;
$dotenv = Dotenv\Dotenv::createImmutable($realpath);
$dotenv->load();
$controllerDirectory = $realpath . "/" . $_ENV['CONTROLLERPATH'];
$loaderTmpDir = $realpath . "/" . $_ENV['LOADERTMPDIR'];

// Require framework (autoload controllers)
$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory($controllerDirectory);
$loader->setTempDirectory($loaderTmpDir); // use 'temp' directory for cache
$loader->register(); // Run the RobotLoader

//Use The namespace for the framework in composer
USE Nickyeoman\Framework;
//create the class for the router
$router = new Nickyeoman\Framework\Router();
//prep the class name for call user func array
$classname=$router->controller . 'Controller';
//Load the child controller
call_user_func_array( array(new $classname(), $router->action), $router->params );
