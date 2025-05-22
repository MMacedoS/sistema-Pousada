<?php

namespace App\Http\Controllers\v1\Page404;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;

class NotFoundController extends Controller {
    
    public function __construct() 
    {      
        parent::__construct();
    }

    public function index(Request $request) 
    {
        $this->router->view(
            "404/index", 
            [
                'page' => $this->router->userLogged() ? "/dashboard" : "/login" 
            ]
        );
    }
}