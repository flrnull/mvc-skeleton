<?php

/**
 * Main global configuration.
 * If you need to change it, create you own file with name config.php.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

define('DS', DIRECTORY_SEPARATOR);

return array(
    // DB config
    'mysql' => array(
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'project',
        'user' => 'root',
        'pass' => '',
    ),
    
    // Project paths
    'paths' => array(
        'baseUrl'   => '',
        'root' => __DIR__ . DS . '..' . DS,
        'templates' => __DIR__ . DS . 'templates' . DS,
        'services' => __DIR__ . DS . 'services' . DS,
        'models' => __DIR__ . DS . 'models' . DS,
        'controllers' => __DIR__ . DS . 'controllers' . DS,
        'framework' => __DIR__ . DS . '..' . DS . 'framework' . DS,
        'vendor' => __DIR__ . DS . '..' . DS . 'vendor' . DS,
        'resources' => __DIR__ . DS . 'resources' . DS,
        'logs' => __DIR__ . DS . '..' . DS . "logs" . DS,
    ),
    
    // Autoload namespaces paths
    'autoload' => array(
        'PDOChainer' => 'php-pdo-chainer/src',
        'AltoRouter' => 'AltoRouter',
        'Pimple'     => 'Pimple/lib',
        'Twig-1.3'   => 'Twig/lib',
        'Monolog'    => 'monolog/src',
        'Psr'        => 'log',
        'Michelf'    => 'php-markdown',
    ),
    
    // IoC
    'container' => array(
        'ioc'      => 'Pimple',
        'router'   => 'Router',
        'request'  => 'Request',
        'template' => 'Template',
        'db'       => 'PDOChainer\PDOChainer',
        'services' => 'ServiceLocator',
    ),
    
    // Misc
    'errors' => true,
    'templatesCache' => false,
);
