<?php

namespace App\Http\Controllers\v1\Balance;

use App\Http\Controllers\Controller;
use App\Repositories\Balance\CaixaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class CaixaController extends Controller
{
    protected $caixaRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->caixaRepository = new CaixaRepository();  
    }

    public function index(Request $request) {
        $reserva = $this->caixaRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($reserva, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'caixas' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('payments/index', ['active' => 'financeiro', 'data' => $data]);
    }

    public function storeByJson(Request $request) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'starting_balance' => 'required'
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            exit();
       } 

        $data['current_balance'] = $data['starting_balance'];
        $data['id_usuario'] = $_SESSION['user']->code;       
        $data['opening_date'] = Date('Y-m-d H:i:s');
           
        $created = $this->caixaRepository->create($data);
    
        if(is_null($created)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'caixa não criado.']);
            return;
        }

        $_SESSION['balance'] = $created;
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'caixa criado']);
        exit();
    }

}