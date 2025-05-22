<?php

namespace App\Http\Controllers\v1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;

class ConfiguracaoController extends Controller 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->router->view("Settings/index");
    }
}