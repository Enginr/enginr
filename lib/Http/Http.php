<?php

namespace Enginr\Http;

use Enginr\Exception\HttpException;
use Enginr\Console\Console;

class Http {
    /**
     * The end of line chars
     * 
     * @var string An end of line chars
     */
    const CRLF = "\r\n";

    /**
     * The space char
     * 
     * @var char A space char
     */
    const SP   = ' ';

    protected $_client;
    
    protected $_line;

    public $headers;

    public $body;

    public function __construct($client, string $buffer) {
        if (!is_resource($client))
            throw new HttpException('1st parameter must be a type of resource. ' .
            gettype($client) . ' given.');
        
        $blocks = explode(self::CRLF . self::CRLF, $buffer);
        $firstBlock  = explode(self::CRLF, $blocks[0]);

        $this->_client = $client;       
        $this->_line = explode(self::SP, $firstBlock[0]);
        $this->headers = $this->_parseHeaders($firstBlock);
        $this->body = $blocks[1];
    }

    private function _parseHeaders(array $firstBlock): array {
        $firstBlock = array_splice($firstBlock, 1, count($firstBlock));
        $headers = [];

        foreach ($firstBlock as $header) {
            $header = explode(': ', $header);
            $headers[$header[0]] = $header[1];
        }

        return $headers;
    }
}