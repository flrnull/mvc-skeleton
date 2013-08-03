<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

class ServiceLocator extends ArrayObject {
    
    protected $container;
    
    function __construct($container) {
        $this->container = $container;
    }
    
    public function get($serviceClassName) {
        if (!isset($this[$serviceClassName])) {
            $this[$serviceClassName] = new $serviceClassName($this->container);
        }
        return $this[$serviceClassName];
    }
}