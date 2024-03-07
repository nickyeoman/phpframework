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
        preg_match_all('/{([^}]+)}/', $path, $matches);
        $parameterNames = $matches[1];
        $pattern = preg_replace('/{([^}]+)}/', '([^\/]+)', $path);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        preg_match($pattern, $url, $matches);
        array_shift($matches);

        return array_combine($parameterNames, $matches);
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
