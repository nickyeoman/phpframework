<?php
namespace Nickyeoman\Framework\Classes;

use Symfony\Component\Yaml\Yaml; // Route files are Yaml

// This class is responsible for routing requests to the appropriate controller and action based on defined routes.
class Router {
    
    private $requestURI; // REQUEST_URI
    private $uriSegments = []; // Request uri but an array
    private $routeFiles = []; // Array of Files to find the routes
    private $routes = []; // Routes as an array 

    public $params = [];
    public $controller = null; // Controller to Call
    public $action = 'index'; // Method to call

    public function __construct($uri = null) {

        if (empty($uri))
            $uri = $_SERVER['REQUEST_URI'];

        $this->requestURI = $uri; // Set uri
        $this->uriSegments = $this->uri2array($uri);  // create uri array
        $this->routeFiles = $this->createRouteFilesArray(); // Load file paths to array
        $this->routes = $this->loadRoutes(); // Read the filepaths and store routes to array
        $this->setCAP();
        
    }

    /**
     * Takes the uri provided and turns it into an array
     * Returns the array
     */
    private function uri2array($uri) {

        // Split the path into segments separated by /
        $urlArray = explode('/', trim($uri, '/'));
    
        // Set all segments after the first two to the parameters array
        return $urlArray;

    }

    /**
     * Creates the default paths for route files
     */
    private function createRouteFilesArray() {

        $a = array(
        BASEPATH . '/App/routes.yml', 
        FRAMEWORKPATH . 'src/routes.yml'
        );
        
        return $a;

    }

    /**
     * Adds a route path
     * Remember to loadRoutes() again if you use this
     */
    public function addRouteFile($filePath) {

        // Add the file to the begining of the array
        array_unshift($this->routeFiles, $filePath);
        return $this->routeFiles;
    }

    /**
     * Loops through the files and returns a routes array
     */
    private function loadRoutes() {

        $routeElements = [];

        foreach ($this->routeFiles as $file) {
            if (file_exists($file)) {
                $routes = Yaml::parseFile($file)['routes'];
                $routeElements = array_merge_recursive($routeElements, $routes);
            }
        }

        return $routeElements;

    }

    /**
     * Set Controller Action Params
     */
    private function setCAP() {

        $rs = $this->routes;
        $seg = $this->uriSegments;
        $ctl = 'Nickyeoman\Framework\Components\error\errorController';
        $found = false;

        foreach ( $rs as $key => $route) {

            $matchedRouteIdentifier = $key;
            $rpath = $route['path'];

            // Convert route path to a regular expression pattern
            $pattern = preg_replace('/\{([^\/]+)\}/', '(?<$1>[^\/]+)', $rpath);
            $pattern = '#^' . $pattern . '$#';

            // Check if the request URI matches the pattern
            if (preg_match($pattern, $this->requestURI, $matches)) {
                $found = true;
                break;
            }
            
        }

        if ($found) {
            $this->controller = $rs[$matchedRouteIdentifier]['namespace'] . '\\' . $rs[$matchedRouteIdentifier]['controller'];
            $this->action = $rs[$matchedRouteIdentifier]['action'];
            
            $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return true;
        } else {
            $this->controller = $ctl;
            $this->action = 'index';
            return false;
        }

    }

    
}
