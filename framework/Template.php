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
     * Template object constructor.
     *
     * @param Pimple $container
     */
    function __construct(Pimple $container) {
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
     * @param $templateName
     * @param Array $variables Params
     *
     * @return String HTML or JSON or some else
     */
    public function render($templateName, $variables = array()) {
        $loader = new Twig_Loader_Filesystem($this->templatesDir);
        $twig = new Twig_Environment($loader, array(
            'cache' => ($this->container['config']['templatesCache']) ? $this->templatesDir . 'cache' : false,
            'autoescape' => true,
        ));
        $variables['controller'] = $this->controller;
        return $twig->render($templateName, $variables);
    }
}