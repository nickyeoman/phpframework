<?php

use Nickyeoman\Framework\Classes\SessionManager;
use Nickyeoman\Framework\Classes\ViewData;

//Start Session
session_start();
$session = new SessionManager();

// Create view Object
$view = new ViewData($session);

// Debugging
$view->debugDump('framework.php', 'Session Data', $session);
$view->debugDump('framework.php', 'View Data', $view);

