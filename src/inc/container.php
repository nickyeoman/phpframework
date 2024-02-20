<?php
use DI\ContainerBuilder;

// Build the container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

// Register Twig as a service in the container
$container->set(Environment::class, $twig);

