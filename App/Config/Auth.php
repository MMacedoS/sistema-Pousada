<?php

namespace App\Config;

use App\Repositories\Balance\CaixaRepository;
use App\Repositories\File\ArquivoRepository;
use App\Repositories\Permission\PermissaoRepository;
use Firebase\JWT\JWT;

class Auth {
    protected $sessionTimeout = 3600;
    protected $renewTime = 600; 

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }        
    }

    public function login($username) 
    {
        if (is_null($username)) {
            return false; 
        }

        $payload = [
            'username' => (array)$username
        ];

        $token = JWT::encode($payload, $_ENV['SECRET_KEY'],'HS256');                
        setcookie('token', $token, time() + 3600);
        $_SESSION['user'] = $username;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $permissaoRepository = new PermissaoRepository(); 
        $permissions = $permissaoRepository->allByUser((int)$username->code);
        $_SESSION['my_permissions'] = $permissions;
        
        $arquivoRepository = new ArquivoRepository();
        $arquivo = $arquivoRepository->findById((int)$username->arquivo_id);   
        $_SESSION['files'] = $arquivo ?? null;
    
        return true;
    }

    public function logout() {
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['last_activity']);
        setcookie('token', '', time() - 3600);
        session_destroy();
    }

    public function check() 
    {           
        if (isset($_SESSION['user']) && isset($_SESSION['login_time']) && isset($_SESSION['last_activity'])) {
            if ((time() - $_SESSION['login_time']) > $this->sessionTimeout) {
                $this->logout();
                return false;
            }
    
            if ((time() - $_SESSION['last_activity']) < $this->renewTime) {
                $_SESSION['last_activity'] = time();
            }
            return true;
        }
        return false;
    }

    public function user() {
        return $_SESSION['user'] ?? null;
    }
}
