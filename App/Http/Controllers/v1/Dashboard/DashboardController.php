<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;

class DashboardController extends Controller {
  
    public $router;

    public function __construct() {
        parent::__construct();  
    }

    public function index(Request $request)
    {
        return $this->router->view('Dashboard/index', [ 'active' => '']);
    }
}