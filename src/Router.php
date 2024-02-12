<?php
namespace Nickyeoman\Framework;

use Symfony\Component\Yaml\Yaml; // Route files are Yaml

// This class is responsible for routing requests to the appropriate controller and action based on defined routes.
class Router {
    
    private $routes = []; // @var array $routes An array to store all routes 
    private $routefiles = []; // @var array $routefiles An array to store the paths of route files

    public function __construct() { //Loads default route files and initializes routes.

        // Load default route files
        $this->addfile(BASEPATH . '/app/routes.yml'); // Application routes
        $this->addfile(FRAMEWORKPATH . '/src/routes.yml'); // Framework routes

        // Load routes from all route files
        $this->loadRoutes();
        $this->routefiles = array_reverse($this->routefiles);

    }

    private function loadRoutes() { // Load routes from all route files into $this->routes array

        foreach ($this->routefiles as $file) {

            if (file_exists($file)) {

                $routes = Yaml::parseFile($file)['routes'];
                $this->routes = array_merge($this->routes ?? [], $routes);

            }

        }

    }

    public function addfile($file) { // Add a route file to the list of route files.

        $this->routefiles[] = $file;

    }

    public function listfiles() { // Output the list of route files (for debugging purposes).
        
        dump($this->routefiles);

    }

    public function listRoutes() { // Output the list of routes (for debugging purposes).

        dump($this->routes);

    }

    /**
     * Route a given path.
     *
     * @param string $path The path to route.
     * @return string The routed path.
     */
    public function route($path) {

        // Remove the last slash from the path if it exists
        $path = rtrim($path, '/');

        // Set path to '/' if empty
        if (empty($path)) {
            $path = '/';
        }

        return $path;

    }

    /**
     * Get the controller and action for a given request path.
     *
     * @return array|null An array containing the controller and action, or null if no matching route is found.
     */
    public function getControllerAction() {

        $path = $this->route($_SERVER['REQUEST_URI']);

        foreach ($this->routes as $route) {

            if ($route['path'] === $path) {

                $controllerNamespace = $route['namespace'] ?? ''; // Ensure default namespace is provided if not defined in the route
                $controller = $route['controller'];
                $action = $route['action'];

                // Construct the full controller namespace
                $fullControllerNamespace = ($controllerNamespace !== '') ? $controllerNamespace . '\\' . $controller : $controller;

                return [
                    'controller' => $fullControllerNamespace,
                    'action' => $action
                ];

            }

        }

        // If no matching route is found, return null
        return null;

    }

}
// End Router.php File