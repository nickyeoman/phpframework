<?php

namespace Nickyeoman\Framework\Classes;

class ControllerPaths {
    
    private array $cachedRoutes = [];
    private $controllerFiles;

    public function __construct($controllerFiles) {
        $this->controllerFiles = $controllerFiles;
    }

    public function setCache() {
        $theRoutes = $this->getRoutes();
        $cacheFile = BASEPATH . '/tmp/routes.php';
        file_put_contents($cacheFile, '<?php return ' . var_export($theRoutes, true) . ';');
        return $theRoutes;
    }

    private function getRoutes(): array {
        if (!empty($this->cachedRoutes)) {
            return $this->cachedRoutes;
        }
    
        $routes = [];
    
        foreach ($this->controllerFiles as $controllerFile) {
            $namespace = $this->getNamespaceFromFile($controllerFile);
            $className = basename($controllerFile, '.php');
            $controllerClassName = $namespace . '\\' . $className; // Single backslash here
            if (class_exists($controllerClassName)) {
                $reflectionClass = new \ReflectionClass($controllerClassName); // Single backslash here
            } else {
                continue;
            }
            foreach ($reflectionClass->getMethods() as $method) {
                $attributes = $method->getAttributes(\Nickyeoman\Framework\Attributes\Route::class); // Single backslash here
                foreach ($attributes as $attribute) {
                    $routes[] = [[$controllerClassName, $method->getName()], $attribute->newInstance()->path];
                }
            }
        }
    
        $this->cachedRoutes = $routes;
        dump($routes);
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
