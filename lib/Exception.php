<?php

namespace Enginr;

class SocketException extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }

    public static function err($socket): string {
        return socket_strerror(socket_last_error($socket));
    }
}

class HttpException extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}

class EnginrException extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}

class RouterException extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}

class RequestException extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}

class Response extends Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}