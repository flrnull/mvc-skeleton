<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

class ModelFactory {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function get($modelName) {
        $model = $modelName . "Model";
        return new $model($this->container);
    }
}