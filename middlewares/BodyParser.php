<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Middleware;

use Enginr\Http\{Http, Request, Response};
use Enginr\System\System; // For debuging

class BodyParser {
    /**
     * Return the middleware
     * 
     * @param void
     * 
     * @return object The middleware function
     */
    public static function init(): object {
        return function (Request $req, Response $res, callable $next): void {    
            // POST method process
            if (array_key_exists('Content-Type', $req->headers)) {      
                if (preg_match( // Url encoded mode
                    '/application\/x-www-form-urlencoded/', 
                    $req->headers['Content-Type']
                )) $req->body = self::_parseUrlEncoded($req->$body);
                
                if (preg_match( // Form data mode
                    '/multipart\/form-data/', 
                    $req->headers['Content-Type']
                )) $req->body = self::_parseFormData($req->body);
            }
        
            // GET method process
            if (preg_match('/\?((\w)+=(\w)+&?)+$/', $req->realuri, $query))
                $req->body = self::_parseUrlEncoded(substr($query[0], 1, strlen($query[0])));

            $next();
        };
    }

    /**
     * Parse the url encoded string to an object
     * 
     * @param string $query An url encoded
     * 
     * @return object The query parsed
     */
    private static function _parseUrlEncoded(string $query): object {
        $body = (object)[];
        $query = explode('&', $query);
        
        foreach ($query as $peer) {
            $peer = explode('=', $peer);
            $body->{$peer[0]} = $peer[1];
        }

        return $body;
    }

    /**
     * Parse the form data string to an object
     * 
     * @param string $query A form data
     * 
     * @return object The query parsed
     */
    private static function _parseFormData(string $query): object {
        $query = explode(Http::CRLF, $query);
        $body = (object)[];

        for ($i = 1; $i < count($query) -1; $i += 2) {
            $query[$i] = str_replace('Content-Disposition: form-data; name="', '', $query[$i]);
            $query[$i] = explode('"', $query[$i]);
            $body->{$query[$i][0]} = $query[$i][1];
        }

        return $body;
    }
}