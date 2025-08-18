<?php

namespace App\Http\Controllers;

use App\Config\Router;
use App\Config\Session;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Controllers\Traits\HasPermissions;

class Controller
{

    use GenericTrait, UserToPerson, HasPermissions;

    public $router;
    protected $session;
    protected $view;

    public function __construct()
    {
        $this->router = new Router();
        $this->session = new Session();
    }
}
