<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Define your template directories
$loader = new FilesystemLoader([
    BASEPATH . '/' . $_ENV['VIEWPATH'], // Your application's templates
    BASEPATH . '/vendor/nickyeoman/nytwig/src' // Additional templates (e.g., nytwig)
]);

// Add namespaces for the template directories
$loader->addPath(BASEPATH . '/vendor/nickyeoman/nytwig/src', 'nytwig');
$loader->addPath(BASEPATH . '/vendor/nickyeoman/phpframework/src/twig-views', 'cms');

// Create Twig environment
$twig = new Environment($loader, [
    // Optional Twig configuration options
    'debug' => true,
]);
