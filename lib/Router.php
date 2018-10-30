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
use Enginr\System\System; // For debugging

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
    protected function _process(Request $req, Response $res, $route, bool $found = FALSE): void {
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

        // Request route test
        if (($route->method === 'ALL' && preg_match($route->uri->regex, $req->uri)) ||
        (($route->method === $req->method && preg_match($route->uri->regex, $req->uri)))) {
            $req->params = $this->_parseParams($route->uri->uri, $req->uri);

            foreach ($route->handlers as $handler) {
                $handler($req, $res, function() use (&$req, &$res) {
                    $this->_process($req, $res, next($this->_routes), TRUE);
                });
            }

            return;
        }

        // Any route matched
        if ($route = next($this->_routes)) {
            $this->_process($req, $res, $route, $found);

            return;
        }

        // Send the 404 status code if any route uri matched
        if (!$found) {
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
     * @return self
     */
    public function all(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('ALL', $uri, $handlers);

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
     * @return self
     */
    public function get(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('GET', $uri, $handlers);

        return $this;
    }

    /**
     * Add a POST route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @return self
     */
    public function post(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('POST', $uri, $handlers);

        return $this;
    }
    
    /**
     * Add a PUT route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @return self
     */
    public function put(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('PUT', $uri, $handlers);

        return $this;
    }

    /**
     * Add a PATCH route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @return self
     */
    public function patch(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('PATCH', $uri, $handlers);

        return $this;
    }

    /**
     * Add a DELETE route to the collection
     * 
     * @param string $uri An uri to listen
     * @param callable[] $handlers An array of callback functions
     *      @param Request $req An HTTP request
     *      @param Response $res A response module
     * 
     * @return self
     */
    public function delete(string $uri, callable ...$handlers): self {
        $this->_routes[] = $this->_stack('DELETE', $uri, $handlers);

        return $this;
    }

    /**
     * Create a route and return it
     *
     * @param string $method An HTTP method
     * @param string $uri An HTTP uri
     * @param array $handlers An array of callables
     * 
     * @throws RouterException If the uri not begin with '/'
     * 
     * @return object The route created
     */
    private function _stack(string $method, string $uri, array $handlers): object {
        if ($uri[0] !== '/')
            throw new RouterException('The uri must be begin with /');

        return (object)[
            'method' => $method,
            'uri' => (object)[
                'regex' => '/^' . addcslashes(preg_replace('/:(\w)+/', '(\w)+', $uri), '/') . '$/',
                'uri' => $uri
            ],
            'handlers' => $handlers
        ];
    }

    /**
     * Parse the request uri parameters to an object of parameters
     *
     * @param string $routeUri A route original uri
     * @param string $reqUri A request uri
     * 
     * @return object The parameters parsed
     */
    private function _parseParams(string $routeUri, string $reqUri): object {
        $routeUri = explode('/', $routeUri);
        $reqUri = explode('/', $reqUri);
        $params = [];

        foreach ($routeUri as $i => $part) {
            if (preg_match('/:(\w)+/', $part, $var)) {
                $params[substr($var[0], 1, strlen($var[0]))] = $reqUri[$i];
            }
        }

        return (object)$params;
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
            $this->_routes[] = $this->_stackMiddlewares($argv);

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
                if (strlen($route->uri->uri) === 1 && strlen($ruri))
                $route->uri->uri = '';

                $this->_routes[] = $this->_stack(
                    $route->method, 
                    $ruri . $route->uri->uri, 
                    $route->handlers
                );
            } else {
                $this->_routes[] = $this->_stackMiddlewares($route->handlers);
            }
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
     * @return object The middlewares
     */
    private function _stackMiddlewares(array $handlers): object {
        foreach ($handlers as $handler)
            if (!is_callable($handler))
                throw new RouterException('This middleware implementation only needs callables.');

        return (object)['handlers' => $handlers];
    }
}