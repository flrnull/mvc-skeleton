<?php

/**
 * Main app file.
 * Routes and IoC.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

$router->map('GET', '/', array('_controller'=>'MainController', 'action'=>'index'), 'index');