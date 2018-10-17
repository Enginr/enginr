<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr;

use Enginr\Http\{Request, Response};
use Enginr\Console\Console;

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

    /**
     * Process an HTTP request
     * Compare the method and uri, and send the appropriate response to the client
     * 
     * @param Request $req An HTTP request
     * @param Response $res A response module
     * 
     * @return void
     */
    protected function _process(Request $req, Response $res): void {
        if (array_key_exists($req->uri, $this->_routes[$req->method])) {
            foreach ($this->_routes[$req->method][$req->uri] as $handler) {
                $handler($req, $res);
            }
        } else {
            $res->setStatus(404);
            $res->send("Cannot $req->method $req->uri");
        }
    }

    /**
     * Add a GET route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @return self
     */
    public function get(string $uri, callable ...$handlers): self { 
        $this->_routes['GET'][$uri] = $handlers;

        return $this;
    }
}