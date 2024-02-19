<?php
use DI\ContainerBuilder;

// Build the container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

// Set the container as the default one for static methods
\DI\Container::set($container);