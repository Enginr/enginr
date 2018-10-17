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
use Enginr\Exception\RouterException;
use Enginr\Console\Console;

class Router {
    /**
     * The collection of routes
     * 
     * @var array A collection of routes
     */
    protected $_routes;

    /**
     * The middlewares array
     * 
     * @var array An array of middlewares
     */
    protected $_middlewares;

    /**
     * The Router constructor
     * 
     * @param void
     * 
     * @return void
     */
    public function __construct() {
        $this->_middlewares = [];

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
            foreach ($this->_middlewares as $middleware)
                $middleware($req, $res);

            foreach ($this->_routes[$req->method][$req->uri] as $handler)
                $handler($req, $res);
            
            return;
        }

        $res->setStatus(404);
        $res->send("Cannot $req->method $req->uri");
    }

    /**
     * Add handlers to all routes
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @throws RouterException If the uri not begin with /
     * 
     * @return self
     */
    public function all(string $uri, callable ...$handlers): self {
        if ($uri[0] !== '/')
            throw new RouterException('The uri must be begin with /');

        foreach ($this->_routes as $method => $uris)
            $this->_routes[$method][$uri] = $handlers;

        return $this;
    }


    /**
     * Add a GET route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @throws RouterException If the uri not begin with /
     * 
     * @return self
     */
    public function get(string $uri, callable ...$handlers): self {
        if ($uri[0] !== '/')
            throw new RouterException('The uri must be begin with /');

        $this->_routes['GET'][$uri] = $handlers;

        return $this;
    }

    /**
     * Implement middlewares
     * 
     * This method accept 2 types of middlewares :
     *      1. A pair of root URI -> Router
     *      A root URI is combined with the URIs specified in the Router
     * 
     *      2. An array of callables
     *      Before any request process, this handlers will be called first.
     *      Then, the peer of Request/Response will be transfered to classical process route
     * 
     * First type of middleware accepted :
     * 
     * @param string A root URI which all uris will be concatenated
     * @param Router A Router to merge it with this Router
     * 
     * Second type of middleware accepted :
     * 
     * @param callable[] An array of handlers that take a Request and Response
     *      @param Request An HTTP request
     *      @param Response A response module
     * 
     * @return self
     */
    public function use(/* any */): self {
        $nargs = func_num_args();

        if (gettype(func_get_arg(0)) === 'string') {
            if ($nargs === 1)
                throw new RouterException('This middleware config need a Router to work.');

            if (gettype(func_get_arg(1)) === 'object' && 
                get_class(func_get_arg(1)) !== 'Enginr\Router')
                throw new RouterException('Arg 2 must be a type of Router');
                
            $this->_mergeRouters(func_get_arg(0), func_get_arg(1));
        } else {
            $handlers = [];

            for ($i = 0; $i < $nargs; ++$i) {
                if (!is_callable(func_get_arg($i)))
                    throw new RouterException('This middleware config need a callable to work.');
    
                $handlers[] = func_get_arg($i);
            }
    
            $this->_addMiddlewares($handlers);
        }

        return $this;
    }

    /**
     * Merde a Router with this Router
     * 
     * @see Router::use() method
     * 
     * @param string $ruri A root URI
     * @param Router $router A Router object
     * 
     * @return void
     */
    private function _mergeRouters(string $ruri, Router $router): void {
        if ($ruri[0] !== '/')
            throw new RouterException('The root uri must be begin with /');
        
        if (strlen($ruri) === 1) $ruri = '';

        foreach ($router->_routes as $method => $uris) {
            foreach ($uris as $uri => $handlers) {
                if (strlen($uri) === 1 && strlen($ruri)) $uri = '';
                $this->_routes[$method][$ruri . $uri] = $handlers;
            }
        }

        foreach ($router->_middlewares as $middleware)
            $this->_middlewares[] = $middleware;
    }

    /**
     * Add handlers to the middlewares array
     * 
     * @see Router::use() method
     * 
     * @param array $handlers An array of callable
     * 
     * @return void
     */
    private function _addMiddlewares(array $handlers): void {
        foreach ($handlers as $handler)
            $this->_middlewares[] = $handler;
    }
}