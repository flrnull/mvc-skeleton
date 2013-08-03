<?php

/**
 * Abstract controller.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

abstract class Controller {
    
    /**
     * IoC container
     * 
     * @var Pimple
     */
    public $container;
    
    /**
     * @var Template
     */
    protected $template;
    
    /**
     * Static files version.
     * Is used in templates.
     * 
     * @var int
     */
    public $version;
    
    /**
     * Route matched array.
     * 
     * @var Array
     */
    public $route;
    
        
    /**
     * Request object.
     * 
     * @var Request
     */
    public $request;
    
    /**
     * Common controller constructor.
     * 
     * @param Pimple $container
     * @param Array $route
     */
    public function __construct($container, array $route) {
        $this->container = $container;
        $this->template = $container['template'];
        $this->template->setController($this);
        $this->route = $route;
        $this->version = $container['config']['version'];
        $this->request = $container['request'];
    }


    /**
     * Sets IoC container.
     * 
     * @param $container
     * 
     * @return Controller
     */
    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }
    
    /**
     * Sets template engine.
     * 
     * @param Template $template
     * 
     * @return Controller
     */
    public function setTemplate(Template $template) {
        $this->template = $template;
        $this->template->setController($this);
        return $this;
    }
    
    /**
     * Generate URL for specified route.
     * 
     * @param String $routeName
     * @param array $params
     */
    public function generate($routeName, array $params = array()) {
        return $this->container['router']->generate($routeName, $params);
    }
    
    /**
     * Alias for tempalte render.
     * 
     * @param String $templateName
     * @param Array $params
     * 
     * @return String
     */
    public function render($templateName, array $params = array()) {
        return $this->template->render($templateName, $params);
    }
    
    /**
     * Get service from ServiceLocator.
     * 
     * @param String $serviceName
     * 
     * @return Service
     */
    public function getService($serviceName) {
        return $this->container['services']->get($serviceName);
    }
    
    /**
     * HTTP redirect.
     * 
     * @param String $url
     */
    public function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}