<?php
use Nickyeoman\Framework\Classes\Router;

$router = new Router();

// Get the controller and action from the router
$controller = $router->controller;
$action = $router->action;

$view->debugDump('Routing', 'Router Class', $router); //Debug

// Check if the controller class exists
if (class_exists($controller)) {
    // Create an instance of the controller class
    $controllerInstance = new $controller($twig, $view);

    // Check if the action method exists in the controller class
    if (method_exists($controllerInstance, $action)) {
        // Call the action method on the controller instance
        $controllerInstance->$action($router->params);
    } else {
        // Handle case where action method doesn't exist
        echo "Error: Action method '$action' not found in controller '$controller'.";
    }
} else {
    // Handle case where controller class doesn't exist
    echo "Error: Controller class '$controller' not found.";
}