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
use Enginr\Middleware\Session\{UUID, SessionException};
use Enginr\System\System;

class Session {
    /**
     * The properties to not save into storage
     * 
     * @var array An array of properties to not use
     */
    const NOUSE = ['_storage', '_req', '_res', 'NOUSE'];

    /**
     * The storage to save sessions datas
     * 
     * @var array A storage
     */
    private $_storage;

    /**
     * The Request object address
     * 
     * @var Request An object address
     */
    private $_req;

    /**
     * The Response object address
     * 
     * @var Response An object address
     */
    private $_res;

    /**
     * The session id
     * 
     * @var string A session id
     */
    public $_id;

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
            $req->{'session'} = new Session($storage, $req, $res);
            $next();
        };
    }

    /**
     * The Session constructor
     * 
     * @param array &$storage A storage address
     * @param Request &$req A Request object address
     * @param Response &$res A Response object address
     * 
     * @return void
     */
    public function __construct(array &$storage, Request &$req, Response &$res) {
        $this->_storage = &$storage;
        $this->_req = &$req;
        $this->_res = &$res;
        $this->_init();
    }

    /**
     * Initialize the session
     * 
     * @param void
     * 
     * @return void
     */
    private function _init(): void {
        if (!$sessionid = $this->_req->cookie->get('sessionid')) {
            $this->_create();
            return;
        }
        
        if (!$data = @$this->_storage[$sessionid]) {
            $this->_create();            
            return;
        }

        foreach ($data as $prop => $value) $this->{$prop} = $value;
    }

    /**
     * Create a new session
     * 
     * @
     */
    private function _create(): void {
        $this->_id = UUID::v4();
        $this->_res->cookie->set([
            'name'     => 'sessionid',
            'value'    => $this->_id,
            'maxAge'   => 3600,
            'httpOnly' => TRUE
        ]);
    }

    /**
     * Set datas to the session
     * 
     * @param array $data A data to set
     * 
     * @return void
     */
    public function set(array $data): void {
        foreach ($data as $key => $value)
            $this->{$key} = $value;

        $this->_updateStorage();
    }

    /**
     * Update the storage collection
     * 
     * @param void
     * 
     * @return void
     */
    private function _updateStorage(): void {
        foreach (get_object_vars($this) as $prop => $value)
            if (!in_array($prop, self::NOUSE))
                $this->_storage[$this->_id][$prop] = $value;
    }
}