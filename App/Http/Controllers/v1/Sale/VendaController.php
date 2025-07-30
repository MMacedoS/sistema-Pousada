<?php

namespace App\Http\Controllers\v1\Sale;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Repositories\Sale\VendaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class VendaController extends Controller
{
    protected $vendaRepository;   
    protected $reservaRepository;
    protected $produtoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->vendaRepository = new VendaRepository();   
        $this->reservaRepository = new ReservaRepository(); 
        $this->produtoRepository = new ProdutoRepository();  
    }

    public function index(Request $request) 
    {
        $params = $request->getBodyParams();
        empty($params) ? $params['status'] = 1 : $params;
        $sales = $this->vendaRepository->all($params);
        $reserva = $this->reservaRepository->allHosted();
        $product = $this->produtoRepository->all(['status' => 1]); 
        // $perPage = 10;
        // $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        // $paginator = new Paginator($sales, $perPage, $currentPage);
        // $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'sales' => $sales,
            'reservas' => $reserva,
            'products' => $product,
        ];

        return $this->router->view('sale/index', ['active' => 'register', 'data' => $data]); 
    }

    public function create() {
        empty($params) ? $params['status'] = 1 : $params;
        $sales = $this->vendaRepository->all($params);
        $reserva = $this->reservaRepository->allHosted();

        $salesReservaIds = array_map(function($sale) {
            return $sale->id_reserva;
        }, $sales);

        $reservasFiltradas = array_filter($reserva, function($reserva) use ($salesReservaIds) {
            return !in_array($reserva->id, $salesReservaIds); 
        });

        return $this->router->view('sale/create', ['active' => 'register', 'reservas' => $reservasFiltradas]);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();
        
        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'description' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'sale/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = $_SESSION['user']->code;
        
        $created = $this->vendaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('sale/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('vendas/');
    }

    public function edit(Request $request, $id) {
        $saleCurrent = $this->vendaRepository->findByUuid($id);

        if (is_null($saleCurrent)) {
            return $this->router->view('sale/', ['active' => 'register', 'danger' => true]);
        }

        empty($params) ? $params['status'] = 1 : $params;
        $sales = $this->vendaRepository->all($params);
        $reserva = $this->reservaRepository->allHosted();

        $salesReservaIds = array_map(function($sale) use ($saleCurrent) {
            if($sale->id != $saleCurrent->id) {
                return $sale->id_reserva;
            }
        }, $sales);

        $reservasFiltradas = array_filter($reserva, function($reserva) use ($salesReservaIds) {
            return !in_array($reserva->id, $salesReservaIds); 
        });
        
        return $this->router->view('sale/edit', [
            'active' => 'register', 
            'sale' => $saleCurrent,
            'reservas' => $reservasFiltradas
        ]);
    }

    public function update(Request $request, $id) 
    {
        $saleCurrent = $this->vendaRepository->findByUuid($id);

        if (is_null($saleCurrent)) {
            return $this->router->view('sale/', ['active' => 'register', 'danger' => true]);
        }

        $data = $request->getBodyParams();
        
        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'description' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'sale/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = $_SESSION['user']->code;        
        $data['reserve_id'] = empty($data['reserve_id']) ? null : $data['reserve_id'];
        
        $created = $this->vendaRepository->update($data, $saleCurrent->id);

        if(is_null($created)) {            
        return $this->router->view('sale/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('vendas/');
    }
}