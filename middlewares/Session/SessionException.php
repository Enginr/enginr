<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Middleware\Session;

class SessionException extends \Exception {
    public function __construct(string $err, int $code = 0, Exception $prev = null) {
        parent::__construct($err, $code, $prev);
    }
}