<?php

use Nickyeoman\Framework\Classes\Router;

$cachedRoutesFile = BASEPATH . '/tmp/routes.php';
$router = new Router($twig, $view, $session, $request);

if (file_exists($cachedRoutesFile)) {
    // Load cached routes from file
    $rtay = include $cachedRoutesFile;
    
} else {
    // Scan controller directories and create new routes
    $controllerDirectories = [
        BASEPATH . '/App/Controllers',
        FRAMEWORKPATH . '/src/Controllers',
        // Add additional controller directories here as needed
    ];
    
    $controllerScanner = new \Nickyeoman\Framework\Classes\ControllerScanner($controllerDirectories);
    $controllerFiles = $controllerScanner->getControllerFiles();
    
    $ControllerPaths = new \Nickyeoman\Framework\Classes\ControllerPaths($controllerFiles);
    $rtay = $ControllerPaths->setCache();
}

$router->set_cached_routes($rtay);

// Handle incoming HTTP request
$router->handleRequest($_SERVER['REQUEST_URI']);
