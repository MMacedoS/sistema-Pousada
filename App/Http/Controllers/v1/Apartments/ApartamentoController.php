<?php

namespace App\Http\Controllers\v1\Apartments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Request\Request;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Utils\Paginator;

class ApartamentoController extends Controller 
{
    use GenericTrait;

    protected $apartamentoRepository;

    public function __construct(IApartamentoRepository $apartamentoRepository) {
        parent::__construct();  
        $this->apartamentoRepository = $apartamentoRepository;
    }

    public function index(Request $request)
    {
        $params = $request->getQueryParams();

        $apartments = $this->apartamentoRepository->all($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($apartments, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'apartments',
                'apartments' => $paginatedBoards,
                'links' => $paginator->links(),
                'searchFilter' => $params['name'] ?? null,
                'category' => $params['category'] ?? null,
                'situation' => $params['situation'] ?? null
            ]
        ); 
    }

    public function create(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }
    
    public function store(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }

    public function edit(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }

    public function update(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }

    public function destroy(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }

    public function changeSituation(Request $request)
    {
        return $this->router->view(
            'Apartments/index', 
            [
                'active' => 'principal'
            ]
        ); 
    }
}