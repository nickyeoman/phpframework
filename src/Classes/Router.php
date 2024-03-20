<?php

namespace Nickyeoman\Framework\Classes;

use Twig\Environment;

class Router {

    private Environment $twig;
    private $view;
    private $session;
    private $request;
    private $cachedRoutes;

    public function __construct(Environment $twig, $view, $session, $request) {
        $this->twig = $twig;
        $this->view = $view;
        $this->session = $session;
        $this->request = $request;
        $this->cachedRoutes = [];
    }

    public function set_cached_routes(array $cachedRoutes): void {
        $this->cachedRoutes = $cachedRoutes;
    }

    public function handleRequest(string $url): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        foreach ($this->cachedRoutes as $route) {
            [$methodInfo, $path] = $route;
            [$controllerClassName, $method_name] = $methodInfo;
    
            $reflectionMethod = new \ReflectionMethod($controllerClassName, $method_name);
    
            $attributes = $reflectionMethod->getAttributes(\Nickyeoman\Framework\Attributes\Route::class);
    
            if (empty($attributes)) {
                continue;
            }
    
            $routeAttribute = $attributes[0]->newInstance();
    
            $allowedMethods = $routeAttribute->methods ?? ['GET'];
    
            if (!in_array($requestMethod, $allowedMethods)) {
                continue;
            }
    
            if ($this->matchPath($url, $path)) {
                $controller = new $controllerClassName($this->twig, $this->view, $this->session, $this->request);
    
                $parameters = $this->getRouteParameters($url, $path);
                    
                $controller->$method_name(...$parameters);
                return;
            }
        }
    
        $errorController = new \Nickyeoman\Framework\Controllers\error($this->twig, $this->view, $this->session, $this->request);
        $errorController->index();
    }

    private function matchPath(string $url, string $path): bool {
        // Replace parameters with regex patterns
        $pattern = preg_replace('/{([^}]+)}/', '([^/]+)', $path); // Using # as delimiter
        $pattern = '#^' . $pattern . '$#'; // Using # as delimiter
    
        return (bool) preg_match($pattern, $url);
    }

    private function getRouteParameters(string $url, string $path): array {
        // Split the URL into segments
        $urlSegments = explode('/', trim($url, '/'));
        
        // Split the path into segments
        $pathSegments = explode('/', trim($path, '/'));
    
        // Check if the number of segments in URL matches the number of segments in path
        if (count($urlSegments) !== count($pathSegments)) {
            return []; // Return empty array if the lengths don't match
        }
    
        $parms = [];
    
        // Loop through the segments of the path
        foreach ($pathSegments as $index => $segment) {
            // Check if the segment is a parameter (enclosed in curly brackets)
            if (preg_match('/^{([^}]+)}$/', $segment, $matches)) {
                // Get the parameter name from the matches
                $parameterName = $matches[1];
                
                // Set the parameter value from the URL segments
                $parms[$parameterName] = $urlSegments[$index];
            }
        }
    
        return $parms;
    }
        
}
