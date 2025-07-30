<?php

namespace App\Http\Controllers\v1\Reservate;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ConsumoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class ConsumoController extends Controller
{
    protected $consumoRepository;    
    protected $reservaRepository;
    protected $produtoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->reservaRepository = new ReservaRepository();   
        $this->consumoRepository = new ConsumoRepository(); 
        $this->produtoRepository = new ProdutoRepository();    
    }

    public function index(Request $request) {
        $reserva = $this->reservaRepository->allHosted();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($reserva, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $product = $this->produtoRepository->all(['status' => 1]); 

        $data = [
            'reservas' => $paginatedBoards,
            'links' => $paginator->links(),
            'products' => $product
        ];

        return $this->router->view('consumption/index', ['active' => 'consumos', 'data' => $data]);
    }

    public function indexJsonByReservaUuid(Request $request, $id) 
    {
        $reserva = $this->reservaRepository->findByUuid($id);

        if (!$reserva) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }

        $consumos = $this->consumoRepository->all(['reserve_id' => $reserva->id, 'status' => '1']);  
        
        echo json_encode($consumos);
        exit();
    }

    public function storeByJson(Request $request, $reserva_id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'product_id' => 'required',           
            'quantity' => 'required',
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            exit();
       } 
    
        $reserve = $this->reservaRepository->findByUuid($reserva_id);
        
        if (is_null($reserve)) {
            http_response_code(422); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            exit();
        }     

        $product = $this->produtoRepository->findById($data['product_id']);
        
        if (is_null($product)) {
            http_response_code(422); 
            echo json_encode(['error' => 'produto não encontrada.']);
            exit();
        }     

        $data['id_reserva'] = $reserve->id;        
        $data['id_produto'] = $product->id;
        $data['amount'] = $product->price;
        $data['id_usuario'] = 1;
           
        $created = $this->consumoRepository->create($data);
    
        if(is_null($created)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'diaria criada']);
        exit();
    }

    public function showByJson(Request $request, $reserve ,$id) 
    {
        $reserve = $this->reservaRepository->findByUuid($reserve);
        
        if (!$reserve) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }     
        
        $diaria = $this->consumoRepository->findByUuid($id);
        
        if (!$diaria) {
            http_response_code(404); 
            echo json_encode(['error' => 'diaria não encontrada.']);
            return;
        }    
        
        echo json_encode($diaria);
        exit();        
    }

    public function updateByJson(Request $request, $reserva_id, $id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'product_id' => 'required',           
            'quantity' => 'required',
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            exit();
       } 

       $reserve = $this->reservaRepository->findByUuid($reserva_id);
        
       if (!$reserve) {
           http_response_code(404); 
           echo json_encode(['error' => 'Reserva não encontrada.']);
           exit();
       }     
    
        $consumo = $this->consumoRepository->findByUuid($id);
        
        if (!$consumo) {
            http_response_code(404); 
            echo json_encode(['error' => 'consumo não encontrada.']);
            exit();
        }    
        
        $product = $this->produtoRepository->findById($data['product_id']);
        
        if (is_null($product)) {
            http_response_code(422); 
            echo json_encode(['error' => 'produto não encontrada.']);
            exit();
        }     

        $data['id_reserva'] = $reserve->id;        
        $data['id_produto'] = $product->id;
        $data['amount'] = $product->price;

        $data['id_usuario'] = $_SESSION['user']->id;
           
        $updated = $this->consumoRepository->update($data, $consumo->id);
    
        if(is_null($updated)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'consumo não atualizada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'consumo atualizada']);
        exit();
    }

    public function destroyAll(Request $request, $id) {
        $reserve = $this->reservaRepository->findByUuid($id);
        $data = $request->getQueryParams();
        
        if (!$reserve) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }        
        
        $params = explode(',', $data['data']);
        $deleted = $this->consumoRepository->deleteAll($params);

        echo json_encode($deleted);
        exit();
    }

    public function destroy(Request $request, $reserva_id, $id) {

        $reserve = $this->reservaRepository->findByUuid($reserva_id);
        if (!$reserve) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }        
        
        $diaria = $this->consumoRepository->findByUuid($id);
        
        if (!$diaria) {
            http_response_code(404); 
            echo json_encode(['error' => 'diaria não encontrada.']);
            return;
        }     

        $deleted = $this->consumoRepository->delete($diaria->id);

        if (!$deleted) {
            http_response_code(422); 
            echo json_encode(['title' => 'Erro ao deleletar', 'message' => 'diaria não apagada.']);
            return;
        }     

        echo json_encode($deleted);
        exit();
    }
}