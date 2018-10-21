<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\System;

class System {
    /**
     * Writes data to the standard output
     * 
     * @param mixed $data A data to write
     * 
     * @return void
     */
    public static function out($data, bool $crlf = TRUE): void {
        switch (gettype($data)) {
            case 'string':
            case 'boolean':
            case 'double':
            case 'integer':
            case 'NULL':
                echo $data;
                if ($crlf) echo "\n";
                break;
            case 'array':
            case 'object':
                print_r($data);
                break;
            default:
                var_dump($data);
        }
    }

    public static function log($data): void {
        self::out('[' . date('Y-m-d H:i:s') . '] ', FALSE);
        self::out($data);
    }
}