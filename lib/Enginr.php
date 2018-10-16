<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr;

use Enginr\Console\Console;

class Enginr extends \Enginr\Router {
    /**
     * The TCP socket server
     * 
     * @var Socket A TCP socket object
     */
    private $_server;

    /**
     * The Enginr constructor
     * 
     * @param void
     * 
     * @return void
     */
    public function __construct() {
        $this->_server = new \Enginr\Socket();
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
        $this->_server->bind($host, $port);

        if ($handler) $handler();

        $this->_server->watch(function ($client, string $buffer): void {
            Console::log($this->_server->getPeerName($client));
        });
    }
}