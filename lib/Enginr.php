<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr;

use Enginr\{Router, Socket};
use Enginr\Http\{Http, Request, Response};
use Enginr\System\System;
use Enginr\Exception\EnginrException;

class Enginr extends Router {
    /**
     * The TCP socket server
     * 
     * @var Socket A TCP socket object
     */
    private $_server;

    /**
     * The view path root
     * 
     * @var string A view path root
     */
    private $_view;

    /**
     * The Enginr constructor
     * 
     * @param void
     * 
     * @return void
     */
    public function __construct() {
        $this->_server = new Socket();
        $this->_view = '';
        parent::__construct();
    }

    /**
     * Listen client incomming connection an process it
     * 
     * @param string $host An interface to listen
     * @param int $port A port to listen
     * @param callable (optional) $handler NULL A callback function to do things ...
     * 
     * @return void
     */
    public function listen(string $host, int $port, callable $handler = NULL): void {
        $this->_server->create();
        $this->_server->set(Socket::OPTLVL, Socket::OPTNAME, Socket::OPTVAL);
        $this->_server->bind($host, $port);

        if ($handler) $handler();

        $this->_server->watch(function ($client, string $buffer): void {
            $this->_process(
                new Request($client, $buffer),
                new Response($client, $this->_view),
                reset($this->_routes)
            );
        });

        $this->_server->close();
    }

    /**
     * Create a Router with all files that are in the directory specified
     * 
     * @param string $path A static file directory path
     * 
     * @throws EnginrException If the file cannot be read
     * 
     * @return Router The Router created
     */
    public static function static(string $path): Router {
        $router = new Router();
        $path = str_replace('\\', '/', $path);

        $dir = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir);
        $regex = new \RegexIterator($iterator, '/\.(\w)+$/', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regex as $uri => $object) {
            $uri = str_replace('\\', '/', $uri);
            $uri = str_replace($path, '', $uri);
            preg_match('/\.(\w)+$/', $uri, $ext);

            if (($content = file_get_contents($path . $uri)) === FALSE)
                throw new EnginrException('Wrong static file path : ' . $path . $uri);

            $router->get($uri, function (Request $req, Response $res) use ($content, $ext) {
                $res->setHeaders(['Content-Type' => Http::MIMESEXT[$ext[0]]]);
                $res->send($content, FALSE);
            });
        }

        return $router;
    }

    /**
     * Set propterties Enginr
     * 
     * @param string $prop A propertie to set
     * @param string $value A value to allocate to the propertie
     * 
     * @throws EnginrException If $prop is not referenced
     * 
     * @return void
     */
    public function set(string $prop, string $value): void {
        if (!property_exists($this, "_$prop"))
            throw new EnginrException("Unknow propertie $prop");

        $this->{"_$prop"} = $value;
    }
}