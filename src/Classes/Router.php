<?php

namespace Nickyeoman\Framework\Classes;

use Twig\Environment;

class Router {
    private array $controllerFiles;
    private array $cachedRoutes = [];
    private Environment $twig;
    private $view;
    private $session;
    private $request;

    public function __construct(array $controllerFiles, Environment $twig, $view, $session, $request) {
        $this->controllerFiles = $controllerFiles;
        $this->twig = $twig;
        $this->view = $view;
        $this->session = $session;
        $this->request = $request;
    }

    public function handleRequest(string $url) {
        $routes = $this->getRoutes();

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($routes as $route) {
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
    
    

    private function getRoutes(): array {
        if (!empty($this->cachedRoutes)) {
            return $this->cachedRoutes;
        }

        $routes = [];

        foreach ($this->controllerFiles as $controllerFile) {
            $namespace = $this->getNamespaceFromFile($controllerFile);
            $className = basename($controllerFile, '.php');
            $controllerClassName = $namespace . '\\' . $className;
            $reflectionClass = new \ReflectionClass($controllerClassName);
            foreach ($reflectionClass->getMethods() as $method) {
                $attributes = $method->getAttributes(\Nickyeoman\Framework\Attributes\Route::class);
                foreach ($attributes as $attribute) {
                    $routes[] = [[$controllerClassName, $method->getName()], $attribute->newInstance()->path];
                }
            }
        }

        $this->cachedRoutes = $routes;
        return $routes;
    }

    private function getNamespaceFromFile(string $file): string {
        $contents = file_get_contents($file);
        $namespacePattern = '/\bnamespace\s+([a-zA-Z0-9\\\\_]+);/m';
    
        if (preg_match($namespacePattern, $contents, $matches)) {
            return $matches[1];
        }
    
        return '';
    }
    
    
}
