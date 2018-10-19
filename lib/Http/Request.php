<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Http;

use Enginr\Socket;
use Enginr\Http\Http;
use Enginr\Exception\RequestException;

class Request {
    /**
     * The client request method
     * 
     * @var string A request method
     */
    public $method;

    /**
     * The client request URI
     * 
     * @var string A request URI
     */
    public $uri;
    
    /**
     * The real request uri (not processed)
     * 
     * @var string A request uri
     */
    public $realuri;

    /**
     * The client HTTP version
     * 
     * @var string An HTTP version
     */
    public $version;

    /**
     * The HTTP headers
     * 
     * @var array An associative array of HTTP headers
     */
    public $headers;

    /**
     * The HTTP body message
     * 
     * @var string An HTTP body message
     */
    public $body;

    /**
     * The client IP address
     * 
     * @var string An IP address
     */
    public $host;

    /**
     * The client port listening
     * 
     * @var int A port
     */
    public $port;

    /**
     * The Request constructor
     * Hydrate the Request properties
     * 
     * @param resource $client A client socket resource
     * @param string $buffer A client HTTP message
     * 
     * @throws RequestException If $bufferclient is not a type of resource
     * 
     * @return void
     */
    public function __construct($client, string $buffer) {
        if (!is_resource($client))
            throw new RequestException('1st parameter must be a type of resource.');

        $this->_hydrate($client, $buffer);
    }

    /**
     * Valuates the Request properties
     * 
     * @param resource $client A socket resource
     * 
     * @throws RequestException If $client is not a type of resource
     * 
     * @return void
     */
    private function _hydrate($client, string $buffer): void {
        if (!is_resource($client))
            throw new RequestException('1st parameter must be a type of resource.');

        $buffer    = explode(Http::CRLF.Http::CRLF, $buffer);
        $lines     = explode(Http::CRLF, $buffer[0]);
        $startline = explode(' ', $lines[0]);
        $peerName  = Socket::getPeerName($client);
    
        $this->method  = $startline[0];
        $this->realuri = $startline[1];
        $this->version = $startline[2];
        $this->uri     = $this->_parseUri($startline[1]);
        $this->headers = $this->_parseHeaders(array_splice($lines, 1, count($lines)));
        $this->body    = $this->_getBody($buffer);
        $this->host    = $peerName->host;
        $this->port    = $peerName->port;
    }

    /**
     * Parse the HTTP headers to an associative array
     * 
     * @param array $heaers An array of headers
     * 
     * @return array The parsed HTTP headers in an associative array
     */
    private function _parseHeaders(array $headers): array {
        $parsedHeaders = [];

        foreach ($headers as $peer) {
            $peer = explode(': ', $peer);
            $parsedHeaders[$peer[0]] = $peer[1];
        }

        return $parsedHeaders;
    }

    /**
     * Return the uri without query
     * 
     * @param string $uri AN uri to parse
     * 
     * @return string The uri parsed
     */
    private function _parseUri(string $uri): string {
        preg_match('/^\/((\w)+\/?)*/', $uri, $reguri);

        return $reguri[0];
    }

    /**
     * Retreive the body message
     * 
     * @param array $buffer An array of HTTP buffer
     * 
     * @return string The HTTP body
     */
    private function _getBody(array $buffer): string {
        $body = '';

        for ($i = 1; $i < count($buffer); ++$i)
            $body .= $buffer[$i];

        return $body;
    }
}