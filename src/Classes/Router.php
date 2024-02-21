<?php
namespace Nickyeoman\Framework\Classes;

use Symfony\Component\Yaml\Yaml; // Route files are Yaml

// This class is responsible for routing requests to the appropriate controller and action based on defined routes.
class Router {
    
    public $params = [];
    private $urlParts = []; // url into an array
    private $routes = []; // An array to store all routes
    private $route = [
        'controller' => 'errorController',
        'action' => 'index',
        'namespace' => 'Nickyeoman\Framework\Components\error'
    ]; // (match)
    private $routeFiles = [
        BASEPATH . '/app/routes.yml', 
        FRAMEWORKPATH . 'src/routes.yml'
    ];

    public function __construct() {

        // Load routes from all route files
        $this->getUrlParts();
        $this->loadRoutes();
        $this->separateParameters();
        if ( $this->findMatch() ) {

        }

    }

    private function loadRoutes() {
        foreach ($this->routeFiles as $file) {
            if (file_exists($file)) {
                $routes = Yaml::parseFile($file)['routes'];
                $this->routes = array_merge_recursive($this->routes, $routes);
            }
        }
        
    }

    private function separateParameters() {
        // Loop through each route to check for parameters
        foreach ($this->routes as &$route) {
            // Check if the route path contains curly brackets {}
            if (strpos($route['path'], '{') !== false && strpos($route['path'], '}') !== false) {
                // Extract parameter names from the route path and store in $route['params']
                preg_match_all('/{([^}]+)}/', $route['path'], $matches);
                $route['params'] = $matches[1];
                // Remove the entire parameter placeholder from the route path
                $route['path'] = preg_replace('/\/?\{([^\/]+)\}/', '', $route['path']);
            }
        }
        dump($this->routes);die();
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
        
        $this->urlParts = $urlArray;
        
    }

    private function getRouterPathParts($router_path) {
        $segary = explode('/', trim($router_path, '/'));
    
        if (empty($segary[0])) {
            $segary[0] = 'index';
        }
    
        $parameters = [];
        foreach ($segary as $index => $part) {
            // Check if the segment contains curly brackets {}
            if (strpos($part, '{') !== false && strpos($part, '}') !== false) {
                // Remove curly brackets from the segment
                $parameterName = trim($part, '{}');
                // Save the parameter name and its index
                $parameters[$parameterName] = $index;
                // Remove the parameter from the segment array
                unset($segary[$index]);
            }
        }

        $this->params = $parameters;
         
        // Return the segmented array and the parameters
        return $segary;
    }
    
    public function findMatch() {

        // Loop through each route to find a match
        foreach ($this->routes as $route) {
            $routeSegments = $this->getRouterPathParts($route['path']);
            $urlParts = $this->urlParts;
    
            $match = true;
            foreach ($routeSegments as $index => $routeSegment) {
                // Compare segments for non-parameter segments
                if ($routeSegment !== $urlParts[$index]) {
                    $match = false;
                    break; // Exit loop if segments don't match
                } else {
                    dump($routeSegment);
                    dump($index);
                }
            }
    
            // If all segments match, return true
            if ($match) {
                return true;
            }
        }
    
        return false;
    }
    
}
