<?php

/**
 * @license MIT
 * @link https://opensource.org/licenses/MIT The MIT License
 * 
 * @author Arthuchaut
 * @link https://github.com/Arthuchaut
 */

namespace Enginr\Middleware\Session;

use Enginr\Http\{Request, Response};
use Enginr\Middleware\Session\UUID;
use Enginr\System\System;

class Session {
    /**
     * The middleware initializer
     * 
     * @param void
     * 
     * @return object A callable for use
     */
    public static function init(): object {
        $storage = [];
        
        return function (Request &$req, Response &$res, callable $next) use (&$storage) {
            self::_updateStorage($storage);

            if (!$sid = $req->cookie->get('sid'))
                $sid = self::_create($res, $storage);
            else if (!array_key_exists($sid, $storage))
                $sid = self::_create($res, $storage);

            $req->{'session'} = &$storage[$sid];

            $next();
        };
    }

    /**
     * Create a new session
     * 
     * @param Response &$res A Response object address
     * @param array &$storage A storage address
     * 
     * @return string The unique ID generated for identify the session ID
     */
    private static function _create(Response &$res, array &$storage): string {
        $uid = UUID::v4();

        $res->cookie->set([
            'name'     => 'sid',
            'value'    => $uid,
            'maxAge'   => 3600,
            'httpOnly' => TRUE
        ]);

        $storage[$uid] = (object)[
            '_id'  => $uid,
            'date' => new \DateTime()
        ];

        return $uid;
    }

    /**
     * Clean the sessions storage
     * Unset sessions older than 24 hours
     *
     * @param array &$storage A storage address
     * 
     * @return void
     */
    private static function _updateStorage(array &$storage): void {
        foreach ($storage as $_id => $session) {
            if ((new \DateTime())->diff($session->date)->d > 0) {
                unset($storage[$_id]);
            }
        }
    }
}