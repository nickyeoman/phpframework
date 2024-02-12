<?php
require BASEPATH . '/vendor/autoload.php'; // Composer

use Dotenv\Dotenv;
use Tracy\Debugger;
use Nickyeoman\Framework\Router;
use Nickyeoman\Framework\Container;

// Initialize Dotenv
$dotenv = Dotenv::createImmutable(BASEPATH);
$dotenv->load();

// Enable Tracy debugger if DEBUG is set to 'display'
if ($_ENV['DEBUG'] === 'display') {
    Debugger::enable(Debugger::DEVELOPMENT);
}

require FRAMEWORKPATH . '/src/Utility.php'; // Global Functions

// Routing Begins
try {
    // Instantiate the Router class
    $router = new Router();

    // Get the controller and action information
    $routeInfo = $router->getControllerAction();

    if ($routeInfo !== null) {

        // If a matching route is found, extract the controller and action names
        $controllerName = $routeInfo['controller'];
        $actionName = $routeInfo['action'];

    } else {
        $controllerName = 'Nickyeoman\Framework\Components\error\errorController';
        $actionName = 'index';
    }

    // Construct the full controller class name
    $controllerClass = $controllerName;

    // Instantiate the controller class
    $container = Container::getInstance();
    $controllerInstance = new $controllerClass($container);

    // Call the action method on the controller instance
    $controllerInstance->$actionName();

} catch (Exception $e) {
    // Handle exceptions gracefully
    echo "An error occurred: " . $e->getMessage();
}

