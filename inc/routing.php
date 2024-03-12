<?php
use Nickyeoman\Framework\Classes\Router;

// Usage
$controllerDirectories = [
    BASEPATH . '/App/Controllers',
    FRAMEWORKPATH . '/src/Controllers',
    // Add additional controller directories here as needed
];

$controllerScanner = new \Nickyeoman\Framework\Classes\ControllerScanner($controllerDirectories);
$controllerFiles = $controllerScanner->getControllerFiles();

$router = new Router($controllerFiles, $twig, $view, $session, $request);
$router->handleRequest($_SERVER['REQUEST_URI']);