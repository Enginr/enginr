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
     * The template engine used
     * 
     * @var string A template engine name
     */
    private $_template;

    /**
     * The response constructor
     * 
     * @param resource $client A socket resource
     * 
     * @throws ResponseException If $client is not a type of resource
     * 
     * @return void
     */
    public function __construct(&$client, string $view, string $template) {
        if (!is_resource($client))
            throw new ResponseException('1st parameter must be a type of resource.');

        setlocale(LC_TIME, 'en_US.utf8');

        $this->_client = $client;
        $this->_view = $view;
        $this->_template = $template;

        $this->setStatus(200);
        $this->setHeaders([
            'Connection'   => 'keep-alive',
            'Date'         => strftime(
                '%a, %d %b %Y %H:%M:%S GMT', 
                strtotime(gmdate('Y-m-d H:i:s'))
            ),
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
     * Read or compile the template file to an html content
     * 
     * @param string $pathfile A path to the view file
     * @param array $vars = [] An array of variables for the template engine
     * 
     * @throws ResponseException If the file could not be read
     * 
     * @return string The html file compiled
     */
    private function _renderTemplate(string $pathfile, array $vars = []): string {
        switch ($this->_template) {
            case 'pug':
                $pathfile .= !preg_match("/\.pug$/", $pathfile) ? '.pug' : '';

                if (!$content = (new \Pug\Pug())->render($pathfile, $vars))
                    throw new ResponseException("Could not read $pathfile");

                return $content;
                break;
            case 'html':
                $pathfile .= !preg_match("/\.html$/", $pathfile) ? '.html' : '';

                if (($content = file_get_contents($pathfile)) === FALSE)
                    throw new ResponseException("Could not read $pathfile");
                
                return $content;
                break;
            default:
                throw new ResponseException("Unknow template engine : $this->_template.");
        }
    }

    /**
     * Send an html file content
     * 
     * @param string $pathfile A file path
     * @param array $vars = [] An array of variables for the template engine
     * 
     * @return void
     */
    public function render(string $pathfile, array $vars = []): void {
        if (!$this->_view) $realpath = $pathfile;
        else $realpath = "$this->_view/$pathfile";

        $realpath = str_replace('\\', '/', $realpath);

        $this->setHeaders(['Content-Type' => Http::MIMESEXT['.html']]);
        $this->send($this->_renderTemplate($realpath, $vars));
    }

    /**
     * Redirect the client to a specific uri
     * Send a location header to the HTTP response
     * 
     * @param string $uri An HTTP uri
     * 
     * @return void
     */
    public function redirect(string $uri): void {
        $this->setStatus(301);
        $this->setHeaders(['Location' => $uri]);
        $this->send();
    }

    /**
     * Prepare the response and send it
     * 
     * @param mixed (optional) $body A data to send
     * 
     * @throws ResponseException If the response was already sent
     * 
     * @return void
     */
    public function send($body = NULL): void {
        try {
            Socket::write($this->_client, $this->_buildHttpResponse($body));
            $this->end();
        } catch (\Exception $e) {
            throw new ResponseException('Cannot send HTTP headers after they had sent.');
        }
    }

    /**
     * Close the client socket
     * 
     * @param void
     * 
     * @return void
     */
    public function end(): void {
        @Socket::close($this->_client);
    }
}