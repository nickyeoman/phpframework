<?php

use Nickyeoman\Framework\Classes\SessionManager;
use Nickyeoman\Framework\Classes\ViewData;
use Nickyeoman\Framework\Classes\RequestManager;

//Start Session
session_start();
$session = new SessionManager();

// Create view Object
$view = new ViewData($session);

$request = new RequestManager($session, $view);

// Debugging
$view->debugDump('framework.php', 'Session Data', $session);
$view->debugDump('framework.php', 'View Data', $view);
$view->debugDump('framework.php', 'Request Manager', $request);
