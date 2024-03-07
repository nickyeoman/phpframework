<?php

namespace Nickyeoman\Framework\Classes;

class ControllerScanner {
    private array $controllerDirectories;

    public function __construct(array $controllerDirectories) {
        $this->controllerDirectories = $controllerDirectories;
    }

    public function getControllerFiles(): array {
        $controllerFiles = [];
        foreach ($this->controllerDirectories as $directory) {
            $this->scanDirectoryForControllers($directory, $controllerFiles);
        }
        return $controllerFiles;
    }

    private function scanDirectoryForControllers(string $directory, array &$controllerFiles) {
        $files = glob($directory . '/*.php');
        foreach ($files as $file) {
            $controllerFiles[] = $file;
        }

        $subdirectories = glob($directory . '/*', GLOB_ONLYDIR);
        foreach ($subdirectories as $subdirectory) {
            $this->scanDirectoryForControllers($subdirectory, $controllerFiles);
        }
    }
}
