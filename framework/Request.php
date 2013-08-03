<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

class Request {
    
    public $post;
    public $query;
    public $cookies;
    public $files;
    public $server;
    
    /**
     * Creates request from globals.
     * 
     * @return self
     */
    public static function createFromGlobals() {
        return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }
    
    /**
     * Request constructor.
     * 
     * @param array  $query      The GET parameters
     * @param array  $post       The POST parameters
     * @param array  $cookies    The COOKIE parameters
     * @param array  $files      The FILES parameters
     * @param array  $server     The SERVER parameters
     */
    public function __construct(array $query = array(), array $post = array(), array $cookies = array(), array $files = array(), array $server = array()) {
        $this->post = $post;
        $this->query = $query;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
    }
    
}