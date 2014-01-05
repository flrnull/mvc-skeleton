<?php

/**
 * Autoload functions.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

// Load project config
$confPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
if (file_exists($confPath."config.default.php")) {
    $GLOBALS['config'] = include_once $confPath."config.default.php";
}
if (file_exists($confPath."config.php")) {
    include_once $confPath."config.php";
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'common_functions.php';

if ($GLOBALS['config']['errors']) {
    ini_set("display_startup_errors", true);
    ini_set("display_errors", true);
    error_reporting(E_ALL);
}

spl_autoload_register('basicAutoLoad');
set_error_handler('errorHandler');
