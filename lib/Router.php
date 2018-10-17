<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr;

class Router {
    /**
     * The collection of routes
     * 
     * @var array A collection of routes
     */
    protected $_routes;

    /**
     * The Router constructor
     * 
     * @param void
     * 
     * @return void
     */
    public function __construct() {
        $this->_routes = [
            'GET'     => [],
            'POST'    => [],
            'PUT'     => [],
            'DELETE'  => [],
            'PATCH'   => []
        ];
    }

    public function get(string $uri, callable ...$handlers): void {
        $this->_routes['GET'][] = $handlers;
    }
}