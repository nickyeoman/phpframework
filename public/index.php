<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set Env Variables
$_ENV['realpath'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
$_ENV['project_path'] = dirname(__DIR__, 1);

require_once $_ENV['project_path'] . '/vendor/nickyeoman/phpframework/src/Bootstrap.php';