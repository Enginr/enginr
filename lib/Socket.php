<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr;

use Enginr\Exception\SocketException;
use Enginr\System\System;

class Socket {
    /**
     * The server memory limit (in bytes)
     * 
     * @var int A Bytes memory size
     */
    const MEMORY_LIMIT = 2147483648;

    /**
     * The socket domain
     * 
     * @var int A protocol family 
     */
    const DOMAIN = AF_INET;

    /**
     * The socket communcation type
     * 
     * @var int A communication type
     */
    const TYPE = SOCK_STREAM;

    /**
     * The socket protocol used for
     * 
     * @var int A protocol
     */
    const PROTOCOL = SOL_TCP;

    /**
     * The protocol level
     * 
     * @var int A protocol level
     */
    const OPTLVL = SOL_SOCKET;

    /**
     * The socket option name
     * 
     * @var int A socket option name
     */
    const OPTNAME = SO_REUSEADDR;

    /**
     * The socket option value
     * 
     * @var mixed A socket option value
     */
    const OPTVAL = 1;

    /**
     * The maximum of incomming connection 
     * will be queued for processing
     * 
     * @var int A maximum incomming connection
     */
    const BACKLOG = SOMAXCONN;

    /**
     * The maximum byte length of the readed buffer
     * 
     * @var int A maximum byte length
     */
    const BUFFER_LEN = 2048;

    /**
     * The time of watch interruption
     * 
     * @var int A time in microsecond
     */
    const USLEEP = 5000;

    /**
     * The main socket
     * 
     * @var resource A socket
     */
    private $_socket;

    /**
     * Create a new socket
     * 
     * @param void
     * 
     * @throws SocketException If the socket could not be created
     * 
     * @return void
     */
    public function create(): void {
        if (!$this->_socket = @socket_create(self::DOMAIN, self::TYPE, self::PROTOCOL))
            throw new SocketException(SocketException::err($this->_socket));

        ini_set('memory_limit', self::MEMORY_LIMIT);

        socket_set_nonblock($this->_socket);
    }

    /**
     * Set option to the main socket
     * 
     * @param int $optlvl An option level
     * @param int $optname An option name
     * @param int $optval An option value
     * 
     * @throws SocketException If the option could not be setted
     * 
     * @return void
     */
    public function set(int $optlvl, int $optname, int $optval): void {
        if (!@socket_set_option($this->_socket, $optlvl, $optname, $optval))
            throw new SocketException(SocketException::err($this->_socket));
    }

    /**
     * Bind a host and port to the main socket
     * then listen it
     * 
     * @param string $host An host to bind
     * @param int $port A port to bind
     * 
     * @throws SocketException If the socket could not be bound
     * @throws SocketException If the socket could not listening
     * 
     * @return void
     */
    public function bind(string $host, int $port): void {
        if (!@socket_bind($this->_socket, $host, $port))
            throw new SocketException(SocketException::err($this->_socket));

        if (!@socket_listen($this->_socket, self::BACKLOG))
            throw new SocketException(SocketException::err($this->_socket));
    }

    /**
     * Watch incomming connection and process it
     * 
     * @param callable $handler A callback function to process incomming connection
     *      @param resource $client A client requester
     *      @param string $buffer A data sent from the client
     * @param array $queue A client connections pile
     * 
     * @return void
     */
    public function watch(callable $handler, array $queue = []): void {
        if ($client = socket_accept($this->_socket)) {
            socket_set_nonblock($client);
            $queue[] = $client;
        }

        foreach ($queue as $i => $client) {
            if (is_resource($client)) {
                if ($buffer = socket_read($client, self::BUFFER_LEN)) {
                    $handler($client, $buffer);
                }
            } else unset($queue[$i]);
        }
        
        usleep(self::USLEEP);
        $this->watch($handler, $queue);
    }

    /**
     * Write to a socket
     * 
     * @param resource $socket A receiver socket
     * @param string $buffer A message to send to the socket
     * 
     * @throws SocketException If $socket is not a type of resource
     * @throws SocketException If the message could not be sent
     * 
     * @return void
     */
    public static function write($socket, string $buffer): void {
        if (!is_resource($socket))
            throw new SocketException('1st parameter must be a type of resource.');

        if (!socket_write($socket, $buffer))
            throw new SocketException('Unable to write to the socket.');
    }

    /**
     * Return a strucutre of host and port of the socket speficied
     * 
     * @param resource $socket A socket to get peer name
     * 
     * @throws SocketException If the socket is not a type of resource
     * @throws SocketException If the peer name could not be retreived
     * 
     * @return object An object of [host, port]
     */
    public static function getPeerName($socket): object {
        $host = null;
        $port = null;
        
        if (!is_resource($socket))
            throw new SocketException('1st parameter must be a type of resource.');

        if (!socket_getpeername($socket, $host, $port))
            throw new SocketException('Could not get socket peer name.');

        return (object)['host' => $host, 'port' => $port];
    }

    /**
     * Close a socket
     * If not socket specified, then close the main socket
     * 
     * @param resource (optional) &$socket A socket address to free
     * 
     * @throws SocketException If the socket is not a type of resource
     * 
     * @return void
     */
    public function close(&$socket = NULL): void {
        if (!$socket) {
            socket_close($this->_socket);
            unset($this->_socket);
            return;
        }

        if (!is_resource($socket))
            throw new SocketException('1st parameter must be a type of resource.');

        socket_close($socket);
        unset($socket);
    }
}