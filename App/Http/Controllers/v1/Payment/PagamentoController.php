<?php

namespace App\Http\Controllers\v1\Payment;

use App\Http\Controllers\Controller;
use App\Repositories\Payment\PagamentoRepository;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ConsumoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Repositories\Sale\VendaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class PagamentoController extends Controller
{
    protected $consumoRepository;    
    protected $reservaRepository;
    protected $produtoRepository;
    protected $pagamentoRepository;
    protected $vendaRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->reservaRepository = new ReservaRepository();   
        $this->consumoRepository = new ConsumoRepository(); 
        $this->produtoRepository = new ProdutoRepository(); 
        $this->pagamentoRepository = new PagamentoRepository();   
        $this->vendaRepository = new VendaRepository();  
    }

    public function index(Request $request) {
        $reserva = $this->reservaRepository->allHosted();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($reserva, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'reservas' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('payments/index', ['active' => 'consumos', 'data' => $data]);
    }

    public function indexJsonByReservaUuid(Request $request, $id) 
    {
        $reserva = $this->reservaRepository->findByUuid($id);

        if (!$reserva) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }

        $pagamentos = $this->pagamentoRepository->all(['reserve_id' => $reserva->id, 'status' => '1']);  
        
        echo json_encode($pagamentos);
        exit();
    }

    public function storeByJson(Request $request, $reserva_id) 
    {
        // $this->checkBalanceOpen();
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'type_payment' => 'required',           
            'payment_amount' => 'required',
            'dt_payment' => 'required'
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

        $data['id_reserva'] = $reserve->id;
        $data['id_usuario'] = $_SESSION['user']->code;       
        $data['id_caixa'] = 1;
           
        $created = $this->pagamentoRepository->create($data);
    
        if(is_null($created)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não criado.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'pagamento criado']);
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
        
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            return;
        }    
        
        echo json_encode($pagamento);
        exit();        
    }

    public function updateByJson(Request $request, $reserva_id, $id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'type_payment' => 'required',           
            'payment_amount' => 'required',
            'dt_payment' => 'required'
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
    
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            exit();
        }    
                
        $data['id_reserva'] = $reserve->id;
        $data['id_usuario'] = $_SESSION['user']->code;       
        $data['id_caixa'] = 1;
           
        $updated = $this->pagamentoRepository->update($data, $pagamento->id);
    
        if(is_null($updated)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não atualizada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'pagamento atualizado']);
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
        $deleted = $this->pagamentoRepository->deleteAll($params);

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
        
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            return;
        }     

        $deleted = $this->pagamentoRepository->delete($pagamento->id);

        if (!$deleted) {
            http_response_code(422); 
            echo json_encode(['title' => 'Erro ao deleletar', 'message' => 'pagamento não apagada.']);
            return;
        }     

        echo json_encode($deleted);
        exit();
    }

    public function indexJsonWithSaleByUuid(Request $request, $id) 
    {
        $venda = $this->vendaRepository->findByUuid($id);

        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }

        $pagamentos = $this->pagamentoRepository->all(['id_venda' => $venda->id, 'status' => '1']);  
        
        echo json_encode($pagamentos);
        exit();
    }
    
    public function storeWithSaleByJson(Request $request, $venda_id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'type_payment' => 'required',           
            'payment_amount' => 'required',
            'dt_payment' => 'required'
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            exit();
       } 
    
        $venda = $this->vendaRepository->findByUuid($venda_id);
        
        if (is_null($venda)) {
            http_response_code(422); 
            echo json_encode(['error' => 'venda não encontrada.']);
            exit();
        }     

        $data['id_venda'] = $venda->id;
        $data['id_usuario'] = $_SESSION['user']->code;       
        $data['id_caixa'] = 1;
           
        $created = $this->pagamentoRepository->create($data);
    
        if(is_null($created)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não criado.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'pagamento criado']);
        exit();
    }

    public function showWithSaleByJson(Request $request, $venda_id ,$id) 
    {
        $venda = $this->vendaRepository->findByUuid($venda_id);
        
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }     
        
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            return;
        }    
        
        echo json_encode($pagamento);
        exit();        
    }

    public function updateWithSaleByJson(Request $request, $venda_id, $id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'type_payment' => 'required',           
            'payment_amount' => 'required',
            'dt_payment' => 'required'
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            exit();
       } 

       $venda = $this->vendaRepository->findByUuid($venda_id);
        
       if (!$venda) {
           http_response_code(404); 
           echo json_encode(['error' => 'venda não encontrada.']);
           exit();
       }     
    
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            exit();
        }    
                
        $data['id_venda'] = $venda->id;
        $data['id_usuario'] = $_SESSION['user']->code;       
        $data['id_caixa'] = 1;
           
        $updated = $this->pagamentoRepository->update($data, $pagamento->id);
    
        if(is_null($updated)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não atualizada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'pagamento atualizado']);
        exit();
    }

    public function destroyWithSaleAll(Request $request, $id) {
        $venda = $this->vendaRepository->findByUuid($id);
        $data = $request->getQueryParams();
        
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }        
        
        $params = explode(',', $data['data']);
        $deleted = $this->pagamentoRepository->deleteAll($params);

        echo json_encode($deleted);
        exit();
    }

    public function destroyWithSale(Request $request, $venda_id, $id) {

        $venda = $this->vendaRepository->findByUuid($venda_id);
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }        
        
        $pagamento = $this->pagamentoRepository->findByUuid($id);
        
        if (!$pagamento) {
            http_response_code(404); 
            echo json_encode(['error' => 'pagamento não encontrada.']);
            return;
        }     

        $deleted = $this->pagamentoRepository->delete($pagamento->id);

        if (!$deleted) {
            http_response_code(422); 
            echo json_encode(['title' => 'Erro ao deleletar', 'message' => 'pagamento não apagada.']);
            return;
        }     

        echo json_encode($deleted);
        exit();
    }
}