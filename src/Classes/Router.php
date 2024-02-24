<?php
namespace Nickyeoman\Framework\Classes;

use Symfony\Component\Yaml\Yaml; // Route files are Yaml

// This class is responsible for routing requests to the appropriate controller and action based on defined routes.
class Router {
    
    public $controller = 'errorController';
    public $action = 'index';
    public $params = [];
    private $urlSegments = []; // url into an array
    private $routes = []; // An array to store all routes
    private $route = [
        'controller' => 'errorController',
        'action' => 'index',
        'namespace' => 'Nickyeoman\Framework\Components\error'
    ];
    private $routeFiles = [
        BASEPATH . '/app/routes.yml', 
        FRAMEWORKPATH . 'src/routes.yml'
    ];

    public function __construct() {

        // Load routes from all route files
        $this->getUrlParts();
        $this->loadRoutes();
        $this->findInstructions();
    }

    private function loadRoutes() {
        foreach ($this->routeFiles as $file) {
            if (file_exists($file)) {
                $routes = Yaml::parseFile($file)['routes'];
                $this->routes = array_merge_recursive($this->routes, $routes);
            }
        }
        
    }

    public function getUrlParts() {
        // Remove query string from the URL, if present
        $urlParts = explode('?', $_SERVER['REQUEST_URI']);
        $path = $urlParts[0];
    
        // Split the path into segments separated by /
        $urlArray = explode('/', trim($path, '/'));
    
        // If the URL array is empty, set the first segment to 'index'
        if (empty($urlArray[0])) {
            $urlArray[0] = 'index';
        }
    
        // Set all segments after the first two to the parameters array
        $this->urlSegments = $urlArray;

    }

    public function findInstructions() {
        $matchedRouteIdentifier = null; // Initialize variable to store matched route identifier
        
        foreach ($this->routes as $identifier => $route) {
            // Initialize variables
            $checkCtrl = '';
            $checkActn = '';
            
            // Extract controller and action from the route
            if ($route['path'] === '/') {
                $checkCtrl = 'index';
            } else {
                $split = explode('/', trim($route['path'], '/'));
                $checkCtrl = strtolower($split[0]);
                if (isset($split[1])) { // Check if action exists
                    $checkActn = strtolower($split[1]);
                }
            }
    
            // Match controller
            if (strtolower($checkCtrl) === strtolower($this->urlSegments[0])) {
                $this->controller = $route['namespace'] . '\\' . $route['controller'];
                
                // Match action if provided
                if ($checkActn && $checkActn === strtolower($this->urlSegments[1])) {
                    $this->action = $route['action'];
                    $this->params = array_slice($this->urlSegments, 2);
                } else {
                    // If action doesn't match, continue searching for other routes
                    $this->params = array_slice($this->urlSegments, 1);
                    continue;
                }
    
                // Save the matched route identifier before breaking out of the loop
                $matchedRouteIdentifier = $identifier;
                
                // Break the loop if both controller and action match
                break;
            }
        }
    
        // Return controller
        return $this->controller;
    }    
    
}
