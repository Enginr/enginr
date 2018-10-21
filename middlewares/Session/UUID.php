<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Middleware\Session;

class UUID {
    /**
     * Generate a random unique ID
     * 
     * @param void
     * 
     * @return string A random unique ID
     */
    public static function v4(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), 
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), 
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}