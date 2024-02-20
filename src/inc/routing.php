<?php
use Nickyeoman\Framework\Router;

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
    $controllerInstance = new $controllerClass($twig);

    // Call the action method on the controller instance
    $controllerInstance->$actionName();

} catch (Exception $e) {
    // Handle exceptions gracefully
    echo "An error occurred: " . $e->getMessage();
}

