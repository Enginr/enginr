<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Exception;

class SocketException extends \Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }

    public static function err($socket): string {
        return socket_strerror(socket_last_error($socket));
    }
}