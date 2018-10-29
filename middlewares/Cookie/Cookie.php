<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Middleware\Cookie;

use Enginr\Http\{Request, Response};
use Enginr\Middleware\Cookie\CookieException;
use Enginr\System\System;

class Cookie {
    /**
     * The middleware initializer
     * 
     * @param void
     * 
     * @return object A callable to use
     */
    public static function init(): object {
        return function (Request &$req, Response &$res, callable $next) {
            $req->{'cookie'} = self::_initRequestCookie($req);
            $res->{'cookie'} = self::_initResponseCookie($res);

            $next();
        };
    }

    /**
     * Set a Cookie class wrote for Request class
     * 
     * @param Request &$req A Request object address
     * 
     * @return object An instance of the anonymouse class
     */
    private static function _initRequestCookie(Request &$req): object {
        return new class($req) {
            /**
             * The Request object address
             * 
             * @var Request An object address
             */
            private $_req;

            /**
             * The anonymouse class constructor
             * 
             * @param Request &$req A Request object address
             * 
             * @return void
             */
            public function __construct(Request &$req) {
                $this->_req = &$req;
            }

            /**
             * Try to retrieve a cookie by name
             * 
             * @param string A name to identify the cookie
             * 
             * @return string|bool The cookie retrieved or FALSE
             */
            public function get(string $name) {
                if (!array_key_exists('Cookie', $this->_req->headers))
                    return FALSE;

                foreach (explode('; ', $this->_req->headers['Cookie']) as $peer) {
                    $peer = explode('=', $peer);
                    if ($peer[0] === $name) return $peer[1];
                }

                return FALSE;
            }
        };
    }

    /**
     * Set a Cookie class wrote for Response class
     * 
     * @param Response &$res A Response object address
     * 
     * @return object An instance of the anonymouse class
     */
    private static function _initResponseCookie(Response &$res): object {
        return new class($res) {
            /**
             * The required keys to specify when set a new cookie
             * 
             * @var array An array of required cookie keys
             */
            const REQUIRED = ['name', 'value'];

            /**
             * The Response object address
             * 
             * @var Response An object address
             */
            private $_res;

            /**
             * The anonymouse class constructor
             * 
             * @param Response &$res A Response object address
             * 
             * @return void
             */
            public function __construct(Response &$res) {
                $this->_res = &$res;
            }

            /**
             * Set a new cookie to the client
             * 
             * @param array $param A parameters to use for setting the cookie
             * 
             * @return void
             */
            public function set(array $param): void {
                foreach (self::REQUIRED as $name)
                    if (!array_key_exists($name, $param))
                        throw new CookieException('Parameter "' . $name . '" missing.');
                
                $cookie  = $param['name'] . '=' . $param['value'];
                $cookie .= @$param['maxAge'] ? '; max-age=' . $param['maxAge'] : ''; 
                $cookie .= @$param['httpOnly'] ? '; HttpOnly' : '';
                $cookie .= @$param['secure'] ? '; Secure' : '';
                $cookie .= '; Path=/';

                $this->_res->setHeaders(['Set-Cookie' => $cookie]);
            }
        };
    }
}