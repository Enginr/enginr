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
use Enginr\Exception\ResponseException;
use Enginr\System\System; // Just for dev debugging

class Response {
    /**
     * The client socket resource
     * 
     * @var resource A socket resource
     */
    private $_client;

    /**
     * The HTTP start line
     * 
     * @var string An HTTP start line
     */
    private $_startline;

    /**
     * The HTTP headers
     * 
     * @var array An HTTP headers
     */
    private $_headers;

    /**
     * The view path root
     * 
     * @var string A view path root
     */
    private $_view;

    /**
     * The response constructor
     * 
     * @param resource $client A socket resource
     * 
     * @throws ResponseException If $client is not a type of resource
     * 
     * @return void
     */
    public function __construct($client, string $view) {
        if (!is_resource($client))
            throw new ResponseException('1st parameter must be a type of resource.');

        $this->_client = $client;
        $this->_view = $view;
        $this->setStatus(200);
        $this->setHeaders([
            'Connection'   => 'keep-alive',
            'Date'         => date('D d M Y H:i:s e'),
            'Content-Type' => 'text/plain',
            'X-Powered-By' => 'Enginr'
        ]);
    }

    /**
     * Parse the headers array into a string
     * 
     * @param void
     * 
     * @return string The headers to string
     */
    private function _headersToString(): string {
        $headers = '';

        foreach ($this->_headers as $name => $values) {
            $headers .= $name . ': ';

            if (is_array($values)) {
                foreach ($values as $i => $value) {
                    $headers .= $value;
                    if ($i < count($values) - 1) $headers .= ',';
                }
            } else $headers .= $values;

            $headers .= Http::CRLF;
        }

        return $headers;
    }

    /**
     * Initialize the HTTP headers propertie
     * If HTTP headers already exists, merge it with the headers specified
     * 
     * @param array $headers An associative array of headers
     * 
     * @throws ResponseException If $headers is not an associative array
     * 
     * @return void
     */
    public function setHeaders(array $headers): void {
        foreach ($headers as $name => $value)
            if (is_int($name))
                throw new ResponseException('1st parameter must be an associative array.');

        if (is_array($this->_headers))
            $this->_headers = array_merge($this->_headers, $headers);
        else
            $this->_headers = $headers;
    }

    /**
     * Set the first line of HTTP response
     * 
     * @param int $status An HTTP status code
     * 
     * @return void
     */
    public function setStatus(int $status): void {
        $this->_startline = Http::VERSION . ' ' . $status . ' ' . Http::STATUS[$status];
    }

    /**
     * Build the HTTP response
     * 
     * @param mixed (optional) $body An HTTP body
     * 
     * @return string The HTTP response
     */
    private function _buildHttpResponse($body = NULL): string {
        return $this->_startline . 
               Http::CRLF .
               $this->_headersToString() . 
               Http::CRLF . Http::CRLF .
               $this->_bodyToString($body);
    }

    /**
     * Parse the body to a string
     * 
     * @param mixed $body A body to parse
     * 
     * @return string The parsed body
     */
    private function _bodyToString($body): string {
        switch (gettype($body)) {
            case 'array':
            case 'object':
                return json_encode($body);
                break;
            default:
                return (string)$body;
        }
    }

    /**
     * Get the appropriate extension of the body
     * 
     * @param mixed body A body to process
     * 
     * @return string The extension evaluated
     */
    public function getExt($body): string {
        switch (gettype($body)) {
            case 'array':
            case 'object':
                return '.json';
                break;
            default:
                return '.html';
        }
    }

    /**
     * Send a file content
     * Set the appropriate MIME type to the HTTP headers
     * 
     * @param string $pathfile A file path
     * 
     * @throws ResponseException If the file cannot be read
     * 
     * @return void
     */
    public function render(string $pathfile): void {
        if (!$this->_view) $realpath = $pathfile;
        else $realpath = "$this->_view/$pathfile";

        $realpath = str_replace('\\', '/', $realpath);

        if (($content = file_get_contents($realpath)) === FALSE)
            throw new ResponseException("Could not read $realpath");

        preg_match('/\.(\w)+$/', $pathfile, $ext);

        $this->setHeaders(['Content-Type' => Http::MIMESEXT[$ext[0]]]);
        $this->send($content, FALSE);
    }

    /**
     * Prepare the response and send it
     * 
     * @param mixed (optional) $body A data to send
     * @param bool (optional) (default = TRUE) $setContentType A bool which specify 
     *     if the method add the apporpriate Content-Type MIME automaticly
     * 
     * @return void
     */
    public function send($body = NULL, bool $setContentType = TRUE): void {
        if ($setContentType)
            $this->setHeaders(['Content-Type' => Http::MIMESEXT[$this->getExt($body)]]);

        $this->end($body);
    }

    /**
     * Send a response to the client, then close the socket
     *  
     * @param string $body (optional) A response to send
     * 
     * @throws ResponseException If the response was already sent
     * 
     * @return void
     */
    public function end($body = NULL): void {
        try {
            Socket::write($this->_client, $this->_buildHttpResponse($body));
            Socket::close($this->_client);
        } catch (\Exception $e) {
            throw new ResponseException('Cannot send HTTP headers after they had sent.');
        }
    }
}