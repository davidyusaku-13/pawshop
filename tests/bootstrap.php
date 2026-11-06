<?php

define('PHPUNIT_RUNNING', true);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

date_default_timezone_set('Asia/Jakarta');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/src/helpers.php';

// Safe session init for testing environment
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
