<?php

namespace Enginr\Http;

use Enginr\Socket;
use Enginr\Http\Http;
use Enginr\Exception\RequestException;

class Request extends Http {
    public $host;

    public $port;

    public $method;

    public $uri;

    public $version;

    public function __construct($client, string $buffer) {
        if (!is_resource($client))
            throw new RequestException('1st parameter must be a type of resource. ' .
            gettype($client) . ' given.');

        parent::__construct($client, $buffer);

        $peerName = Socket::getPeerName($this->_client);
        $this->host = $peerName->host;
        $this->port = $peerName->port;
        $this->method = $this->_line[0];
        $this->uri = $this->_line[1];
        $this->version = $this->_line[2];
    }
}