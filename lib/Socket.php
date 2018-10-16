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

class Socket {
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
     * 
     * @return void
     */
    public function watch(callable $handler): void {
        if ($client = socket_accept($this->_socket)) {
            if ($buffer = socket_read($client, self::BUFFER_LEN)) {
                $handler($client, $buffer);
                $this->close($client);
            }
        }

        $this->watch($handler);
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
    public function getPeerName($socket): object {
        $host = null;
        $port = null;
        
        if (!is_resource($socket))
            throw new SocketException('1st parameter must be a type of resource. ' . 
            gettype($socket) . ' given.');

        if (!socket_getpeername($socket, $host, $port))
            throw new SocketException('Could not get socket peer name.');

        return (object)['host' => $host, 'port' => $port];
    }

    /**
     * Close a socket
     * 
     * @param resource &$socket A socket address to free
     * 
     * @throws SocketException If the socket is not a type of resource
     * 
     * @return void
     */
    public function close(&$socket): void {
        if (!is_resource($socket))
            throw new SocketException('1st parameter must be a type of resource. ' . 
            gettype($socket) . ' given.');

        socket_close($socket);
        $socket = null;
    }
}