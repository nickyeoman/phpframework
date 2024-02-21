<?php

use Nickyeoman\Framework\Classes\SessionManager;
use Nickyeoman\Framework\Classes\ViewData;

//Start Session
session_start();
$session = new SessionManager();

// Create view Object
$view = new ViewData($session);

// Debugging Starts Now
if ($_ENV['DEBUG'] === 'display') {
    $view->adddebug('Debugging is Enabled, Session and View loaded.');
} 