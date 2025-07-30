<?php

namespace App\Http\Controllers\v1\Sale;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Repositories\Sale\ItemVendaRepository;
use App\Repositories\Sale\VendaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class ItemVendaController extends Controller
{
    protected $vendaRepository;   
    protected $itemVendaRepository;  
    protected $reservaRepository;
    protected $produtoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->vendaRepository = new VendaRepository();  
        $this->itemVendaRepository = new ItemVendaRepository();   
        $this->reservaRepository = new ReservaRepository(); 
        $this->produtoRepository = new ProdutoRepository();  
    }

    public function indexJsonByReservaUuid(Request $request, $id) 
    {
        $venda = $this->vendaRepository->findByUuid($id);

        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }

        $consumos = $this->itemVendaRepository->all(['id_venda' => $venda->id, 'status' => '1']);  
        
        echo json_encode($consumos);
        exit();
    }

    public function storeByJson(Request $request, $venda_id) 
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
    
        $venda = $this->vendaRepository->findByUuid($venda_id);
        
        if (is_null($venda)) {
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

        $data['id_venda'] = $venda->id;        
        $data['id_produto'] = $product->id;
        $data['amount_item'] = $product->price;
        $data['id_usuario'] = $_SESSION['user']->id;
           
        $created = $this->itemVendaRepository->create($data);
    
        if(is_null($created)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'item não addicionado.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'item adicionado']);
        exit();
    }

    public function showByJson(Request $request, $vendaId ,$id) 
    {
        $venda = $this->vendaRepository->findByUuid($vendaId);
        
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }     
        
        $items = $this->itemVendaRepository->findByUuid($id);
        
        if (!$items) {
            http_response_code(404); 
            echo json_encode(['error' => 'items não encontrada.']);
            return;
        }    
        
        echo json_encode($items);
        exit();        
    }

    public function updateByJson(Request $request, $vendaId, $id) 
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

       $vendas = $this->vendaRepository->findByUuid($vendaId);
        
       if (!$vendas) {
           http_response_code(404); 
           echo json_encode(['error' => 'venda não encontrada.']);
           exit();
       }     
    
        $items = $this->itemVendaRepository->findByUuid($id);
        
        if (!$items) {
            http_response_code(404); 
            echo json_encode(['error' => 'item não encontrada.']);
            exit();
        }    
        
        $product = $this->produtoRepository->findById($data['product_id']);
        
        if (is_null($product)) {
            http_response_code(422); 
            echo json_encode(['error' => 'produto não encontrada.']);
            exit();
        }     

        $data['id_venda'] = $vendas->id;        
        $data['id_produto'] = $product->id;
        $data['amount_item'] = $product->price;
        $data['id_usuario'] = $_SESSION['user']->id;
           
        $updated = $this->itemVendaRepository->update($data, $items->id);
    
        if(is_null($updated)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'item não atualizada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'item atualizado']);
        exit();
    }

    public function destroyAll(Request $request, $id) {
        $venda = $this->vendaRepository->findByUuid($id);
        $data = $request->getQueryParams();
        
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }        
        
        $params = explode(',', $data['data']);
        $deleted = $this->itemVendaRepository->deleteAll($params);

        echo json_encode($deleted);
        exit();
    }

    public function destroy(Request $request, $venda_id, $id) {

        $venda = $this->vendaRepository->findByUuid($venda_id);
        if (!$venda) {
            http_response_code(404); 
            echo json_encode(['error' => 'venda não encontrada.']);
            return;
        }        
        
        $item = $this->itemVendaRepository->findByUuid($id);
        
        if (!$item) {
            http_response_code(404); 
            echo json_encode(['error' => 'item não encontrada.']);
            return;
        }     

        $deleted = $this->itemVendaRepository->delete($item->id);

        if (!$deleted) {
            http_response_code(422); 
            echo json_encode(['title' => 'Erro ao deleletar', 'message' => 'diaria não apagada.']);
            return;
        }     

        echo json_encode($deleted);
        exit();
    }
}