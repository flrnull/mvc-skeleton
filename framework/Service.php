<?php

/**
 * Abstract service.
 * 
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

abstract class Service {
    
    protected $container;
    
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * Returns new model object.
     *
     * @param String $name
     *
     * @return ModelRecord
     */
    public function getModel($name) {
        return $this->container['models']->get($name);
    }

}
