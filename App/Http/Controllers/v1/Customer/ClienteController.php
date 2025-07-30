<?php

namespace App\Http\Controllers\v1\Customer;

use App\Config\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Customer\ClienteRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class ClienteController extends Controller
{
    protected $clienteRepository;
    protected $permissaoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->clienteRepository = new ClienteRepository();
    }

    public function index(Request $request) {
        $cliente = $this->clienteRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($cliente, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'clientes' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('customer/index', ['active' => 'register', 'data' => $data]);
    }

    public function create() {
        return $this->router->view('customer/create', ['active' => 'register']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'customer/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->clienteRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('customer/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('cliente/');
    }

    public function edit(Request $request, $id) {
        $cliente = $this->clienteRepository->findByUuid($id);
        
        if (is_null($cliente)) {
            return $this->router->view('customer/', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->view('customer/edit', ['active' => 'register', 'cliente' => $cliente]);
    }

    public function update(Request $request, $id) {
        $cliente = $this->clienteRepository->findByUuid($id);

        if (is_null($cliente)) {
            return $this->router->view('cliente/', ['active' => 'register', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'customer/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $updated = $this->clienteRepository->update($cliente->id, $data);
        
        if(is_null($updated)) {            
        return $this->router->view('customer/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('cliente/');
    }

    public function delete(Request $request, $id) {
        $cliente = $this->clienteRepository->findByUuid($id);
        
        if (is_null($cliente)) {
            return $this->router->view('customer/', ['active' => 'register', 'danger' => true]);
        }

        $cliente = $this->clienteRepository->delete($cliente->id);

        return $this->router->redirect('cliente/');
    }
}