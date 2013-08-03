<?php

/**
 * Web bootstrap file.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */
include_once '../framework/autoload.php';

// Init IoC container
$container = include_once '../framework/container.php';

// Routes
$router = $container['router'];
$router->setBasePath($container['config']['paths']['baseUrl']);
include_once '../src/app.php';
$match = $router->match();

// If page not found
if (!$match) {
    exit('Page not found 404');
}

// Controller
$controller = new $match['target']['_controller']($container, $match);

// Run app
echo call_user_func_array(array($controller, $match['target']['action'] . "Action"), $match['params']);