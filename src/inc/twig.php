<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Define your template directories
$loader = new FilesystemLoader([
    BASEPATH . '/' . $_ENV['VIEWPATH'], // Your application's templates
    BASEPATH . '/vendor/nickyeoman/nytwig/src', // Additional templates (e.g., nytwig)
    BASEPATH . '/vendor/nickyeoman/phpframework/src/Components/error/twig/' // Error templates
]);

// Add namespaces for the template directories
$loader->addPath(BASEPATH . '/vendor/nickyeoman/nytwig/src', 'nytwig');
$loader->addPath(BASEPATH . '/vendor/nickyeoman/phpframework/src/Components/error/twig/', 'error');
$loader->addPath(BASEPATH . '/vendor/nickyeoman/phpframework/src/Components/contact/twig/', 'contact');
$loader->addPath(BASEPATH . '/vendor/nickyeoman/phpframework/src/Components/login/twig/', 'login');
$loader->addPath(BASEPATH . '/vendor/nickyeoman/phpframework/src/Components/user/twig/', 'user');

// Create Twig environment
$twig = new Environment($loader, [
    // Optional Twig configuration options
    'debug' => true,
]);
