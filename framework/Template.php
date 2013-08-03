<?php

/**
 * Simple template class.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */
class Template {

    /**
     * Base templates directory.
     * 
     * @var String
     */
    public $templatesDir;
    
    /**
     * @var Controller
     */
    public $controller;
    
    /**
     * @var Pimple
     */
    public $container;
    
    /**
     * Template object contructor.
     * 
     * @param String $templateDir 
     */
    function __construct($container) {
        $this->templatesDir = $container['config']['paths']['templates'];
        $this->container = $container;
        require_once $container['config']['paths']['vendor'] . $container['config']['autoload']['Twig-1.3'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
    }
    
    /**
     * @param Controller $controller
     */
    function setController(Controller $controller) {
        $this->controller = $controller;
    }
    
    /**
     * Renders template.
     * 
     * @param String $viewName View name
     * @param Array $variables Params
     * 
     * @return String HTML or JSON or some else
     */
    public function render($templateName, $variables = array()) {
        $loader = new Twig_Loader_Filesystem($this->templatesDir);
        $twig = new Twig_Environment($loader, array(
            'cache' => ($this->container['config']['templatesCache']) ? $this->templatesDir . 'cache' : false,
        ));
        $variables['controller'] = $this->controller;
        return $twig->render($templateName, $variables);
    }
}