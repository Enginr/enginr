<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Console;

class Console {
    /**
     * Writes data to the standard output
     * 
     * @param mixed $data A data to write
     * 
     * @return void
     */
    public static function log($data): void {
        switch (gettype($data)) {
            case 'string':
            case 'boolean':
            case 'double':
            case 'integer':
            case 'NULL':
                echo "$data\n";
                break;
            case 'array':
            case 'object':
                print_r($data);
                break;
            default:
                var_dump($data);
        }
    }
}