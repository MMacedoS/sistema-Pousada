<?php

namespace App\Http\Middleware;

use App\Config\Auth;

class Authenticate {
    public static function handle() {
        $auth = new Auth();
        if (!$auth->check()) {            
            header('Location: ' . $_ENV['URL_PREFIX_APP']);           
        }
        return;
    }
}
