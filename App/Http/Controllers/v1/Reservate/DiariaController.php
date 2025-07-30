<?php

namespace App\Http\Controllers\v1\Reservate;

use App\Http\Controllers\Controller;
use App\Repositories\Reservate\DiariaRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class DiariaController extends Controller
{
    protected $diariaRepository;
    protected $clienteRepository;    
    protected $apartamentoRepository;
    protected $reservaRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->diariaRepository = new DiariaRepository();    
        $this->reservaRepository = new ReservaRepository();      
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

        return $this->router->view('diaries/index', ['active' => 'consumos', 'data' => $data]);
    }

    public function indexJsonByReservaUuid(Request $request, $id) 
    {
        $reserva = $this->reservaRepository->findByUuid($id);

        if (!$reserva) {
            http_response_code(404); 
            echo json_encode(['error' => 'Reserva não encontrada.']);
            return;
        }

        $diarias = $this->diariaRepository->all(['reserve_id' => $reserva->id, 'status' => '1']);  
        
        echo json_encode($diarias);
        exit();
    }

    public function storeByJson(Request $request, $reserva_id) 
    {
        $data = $request->getBodyParams();
        $validator = new Validator($data);
        $rules = [
            'dt_daily' => 'required',           
            'amount' => 'required',
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
            return;
        }     

        $data['id_reserva'] = $reserve->id;
        $data['id_usuario'] = $_SESSION['user']->code;
           
        $created = $this->diariaRepository->create($data);
    
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
        
        $diaria = $this->diariaRepository->findByUuid($id);
        
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
            'dt_daily' => 'required',           
            'amount' => 'required'
        ];
    
        if (!$validator->validate($rules)) {
            http_response_code(404); 
            echo json_encode(['error' => 'dados invalidos.']);
            return;
       } 

       $reserve = $this->reservaRepository->findByUuid($reserva_id);
        
       if (!$reserve) {
           http_response_code(404); 
           echo json_encode(['error' => 'Reserva não encontrada.']);
           return;
       }     
    
        $diaria = $this->diariaRepository->findByUuid($id);
        
        if (!$diaria) {
            http_response_code(404); 
            echo json_encode(['error' => 'diaria não encontrada.']);
            return;
        }     

        $data['id_usuario'] = $_SESSION['user']->code;
           
        $updated = $this->diariaRepository->update($data, $diaria->id);
    
        if(is_null($updated)) {            
            http_response_code(404); 
            echo json_encode(['error' => 'diaria não atualizada.']);
            return;
        }
    
        echo json_encode(['title' => "sucesso!" ,'message' => 'diaria atualizada']);
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
        $deleted = $this->diariaRepository->deleteAll($params);

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
        
        $diaria = $this->diariaRepository->findByUuid($id);
        
        if (!$diaria) {
            http_response_code(404); 
            echo json_encode(['error' => 'diaria não encontrada.']);
            return;
        }     

        $deleted = $this->diariaRepository->delete($diaria->id);

        if (!$deleted) {
            http_response_code(422); 
            echo json_encode(['title' => 'Erro ao deleletar', 'message' => 'diaria não apagada.']);
            return;
        }     

        echo json_encode($deleted);
        exit();
    }

    public function generateDaily(Request $request, $token) 
    {
        LoggerHelper::logInfo('Iniciando a ação no SomeController');
        if ($token !== SECRET_KEY_DAYLI) {
            echo json_encode(["status" => 401, "message" => "params invalid"]);
            die;
        }       
        
        $genetrate = $this->diariaRepository->generateDaily();
        LoggerHelper::logInfo($genetrate);
        echo json_encode(["status" => 200, "message" => json_encode($genetrate)]);
        exit();
    }
}