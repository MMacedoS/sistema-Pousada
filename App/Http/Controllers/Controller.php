<?php

namespace App\Http\Controllers;

use App\Config\Router;
use App\Config\Session;

class Controller {
  
    public $router;
    protected $session;
    protected $view;

    public function __construct() 
    {
        $this->router = new Router();
        $this->session = new Session();
    }
}