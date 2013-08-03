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
}
