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
use Enginr\System\System; // Just for dev debugging

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
        $this->_routes = [];
    }

    /**
     * Process the HTTP request
     * Tries to match a route and calls the corresponding handlers.
     * If the 'next' function is called, the next route will be tested.
     * 
     * @param Request $req An HTTP request
     * @param Response $res A response module
     * @param array|bool $route The next routes iteration
     * 
     * @return void
     */
    protected function _process(Request $req, Response $res, $route): void {
        if (!$route) return;

        // Middleware test
        if (!property_exists($route, 'method')) {
            foreach ($route->handlers as $handler) {
                $handler($req, $res, function() use (&$req, &$res) {
                    $this->_process($req, $res, next($this->_routes));
                });
            }

            return;
        }
        
        // ALL method or classic methods test
        if (($route->method === 'ALL' && $route->uri === $req->uri) ||
           (($route->method === $req->method && $route->uri === $req->uri))) {
            foreach ($route->handlers as $handler) {
                $handler($req, $res, function() use (&$req, &$res) {
                    $this->_process($req, $res, next($this->_routes));
                });
            }
        }

        // If any response was sent ...
        if (!$res->isSent()) {
            if ($route = next($this->_routes)) {
                $this->_process($req, $res, $route);
                return;
            }

            $res->setStatus(404);
            $res->send("Cannot $req->method $req->uri");
        }
    }

    /**
     * Add a route of fictive ALL method for listening all methods
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

        $this->_routes[] = (object)[
            'method'   => 'ALL',
            'uri'      => $uri,
            'handlers' => $handlers
        ];

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

        $this->_routes[] = (object)[
            'method'   => 'GET',
            'uri'      => $uri,
            'handlers' => $handlers
        ];

        return $this;
    }

    /**
     * Implement middlewares
     * 
     * This method accept 2 types of middlewares :
     *      1. A peer of [root uri] > [Router]
     *      A root uri is combined with the uris specified in the Router
     * 
     *      2. An array of callables
     *      Before any request process, this handlers will be called first.
     *      Then, the peer of Request/Response will be transfered to classical process route
     * 
     * The first type of middleware accepted :
     * @param string A root uri which all uris will be concatenated
     * @param Router A Router to merge it with this Router
     * 
     * The second type of middleware accepted :
     * @param callable[] An array of handlers that take a Request and Response
     *      @param Request An HTTP request
     *      @param Response A response module
     * 
     * @return self
     */
    public function use(/* any */): self {
        $argv = func_get_args();

        // First type of middleware
        if (count($argv) === 2 && gettype($argv[0]) === 'string')
            $this->_merge($argv[0], $argv[1]);

        // Second type of middleware
        else
            $this->_addMiddlewares($argv);

        return $this;
    }

    /**
     * Merde a Router with this Router
     * 
     * @see Router::use() method
     * 
     * @param string $ruri A root uri
     * @param Router $router A Router object
     * 
     * @throws RouterException If $ruri not begin with /
     * 
     * @return void
     */
    private function _merge(string $ruri, Router $router): void {
        if ($ruri[0] !== '/')
            throw new RouterException('The root uri must be begin with /');
        
        if (strlen($ruri) === 1) $ruri = '';

        foreach ($router->_routes as $route) {
            if (property_exists($route, 'uri')) {
                if (strlen($route->uri) === 1 && strlen($ruri)) $route->uri = '';
                $route->uri = $ruri . $route->uri;
            }

            $this->_routes[] = $route;
        }
    }

    /**
     * Add handlers to the middlewares array
     * 
     * @see Router::use() method
     * 
     * @param array $handlers An array of callable
     * 
     * @throws RouterException If there are a none callable handler
     * 
     * @return void
     */
    private function _addMiddlewares(array $handlers): void {
        foreach ($handlers as $handler)
            if (!is_callable($handler))
                throw new RouterException('This middleware implementation only needs callables.');

        $this->_routes[] = (object)['handlers' => $handlers];
    }
}