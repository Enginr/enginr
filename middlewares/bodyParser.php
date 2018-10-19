<?php

use Enginr\Http\{Http, Request, Response};
use Enginr\System\System; // For debuging

/**
 * Parse the request body to an object easy to use
 * Also valuate it with the uri query
 * 
 * Support :
 *      1. The url encoded mode
 *      2. The form data mode
 *      3. The uri query mode
 */
return function ($req, $res, $next) {    
    // POST method process
    if (array_key_exists('Content-Type', $req->headers)) {
        // Url encoded mode
        if (preg_match('/application\/x-www-form-urlencoded/', $req->headers['Content-Type'])) {
            $query = explode('&', $req->body);
            $req->body = (object)[];
            
            foreach ($query as $peer) {
                $peer = explode('=', $peer);
                $req->body->{$peer[0]} = $peer[1];
            }
        }
        
        // Form data mode
        if (preg_match('/multipart\/form-data/', $req->headers['Content-Type'])) {
            $query = explode("\r\n", $req->body);
            $req->body = (object)[];

            for ($i = 1; $i < count($query) -1; $i += 2) {
                $query[$i] = str_replace('Content-Disposition: form-data; name="', '', $query[$i]);
                $query[$i] = explode('"', $query[$i]);
                $req->body->{$query[$i][0]} = $query[$i][1];
            }
        }
    }

    // GET method process
    if (preg_match('/\?((\w)+=(\w)+&?)+$/', $req->realuri, $query)) {
        $query = substr($query[0], 1, strlen($query[0]));
        $query = explode('&', $query);
        $req->body = (object)[];
        
        foreach ($query as $peer) {
            $peer = explode('=', $peer);
            $req->body->{$peer[0]} = $peer[1];
        }
    }

    $next();
};