<?php

namespace App\Http\Controllers\v1\Site;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;

class SiteController extends Controller {
  
    public $router;

    public function __construct() {
        parent::__construct();  
    }

    public function index(Request $request) 
    {
        return $this->router->view(
            'Site/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }
}