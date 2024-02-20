<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', dirname(__DIR__, 1));
define('FRAMEWORKPATH', BASEPATH . '/vendor/nickyeoman/phpframework/');
require_once FRAMEWORKPATH . 'src/Bootstrap.php';
