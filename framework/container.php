<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

// Init
$container = new $GLOBALS['config']['container']['ioc']();

// Config
$container['config'] = $GLOBALS['config'];

// Router
$container['router'] = $container->share(function($c) {
    return new $c['config']['container']['router']();
});

// Request
$container['request'] = $container->share(function($c) {
    return $c['config']['container']['request']::createFromGlobals();
});

// Template
$container['template'] = $container->share(function($c) {
    return new $c['config']['container']['template']($c);
});

// DataBase
$container['db'] = $container->share(function($c) {
    return new $c['config']['container']['db'](array(
        'user' => $c['config']['mysql']['user'],
        'pass' => $c['config']['mysql']['pass'],
        'dbname' => $c['config']['mysql']['dbname'],
    ));
});
$container['currentDbName'] = $container['config']['mysql']['dbname'];

// ServiceLocator
$container['services'] = $container->share(function($c){
    return new $c['config']['container']['services']($c);
});

// Logger
$container['log'] = $container->share(function($c){
    $log = new Monolog\Logger('main');
    $log->pushHandler(new Monolog\Handler\StreamHandler($c['config']['paths']['logs'] . 'main.log'));
    return $log;
});

// Models factory
$container['models'] = $container->share(function($c) {
    return new ModelFactory($c);
});

return $container;