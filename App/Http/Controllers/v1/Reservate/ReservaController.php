<?php

namespace App\Http\Controllers\v1\Reservate;

use App\Http\Controllers\Controller;
use App\Repositories\Apartamento\ApartamentoRepository;
use App\Repositories\Customer\ClienteRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class ReservaController extends Controller
{
    protected $reservaRepository;
    protected $clienteRepository;    
    protected $apartamentoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->reservaRepository = new ReservaRepository();        
        $this->clienteRepository = new ClienteRepository();
        $this->apartamentoRepository = new ApartamentoRepository();
    }

    public function index(Request $request) {
        $reserva = $this->reservaRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($reserva, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'reservas' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('reservate/index', ['active' => 'register', 'data' => $data]);
    }

    public function create() {
        $clientes = $this->clienteRepository->all();
        return $this->router->view('reservate/create', ['active' => 'register', 'customers' => $clientes]);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);
        $rules = [
            'dt_checkin' => 'required',
            'dt_checkout' => 'required',
            'apartament' => 'required',
            'status' => 'required',           
            'amount' => 'required',
            'customers' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'reservate/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = 1;
        
        $created = $this->reservaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('reservate/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('reserva/');
    }

    public function edit(Request $request, $id) {
        $reserva = $this->reservaRepository->findByUuid($id);
        
        if (is_null($reserva)) {
            return $this->router->view('reservate/', ['active' => 'register', 'danger' => true]);
        }

        $reserva = $this->reservaRepository->findByIdWithCustomers($reserva->id);        

        $clientes = $this->clienteRepository->all();

        return $this->router->view('reservate/edit', [
            'active' => 'register', 
            'data' => [
                'reserve' => $reserva, 
                'customers' => $clientes
            ]
        ]);
    }

    public function update(Request $request, $id) {
        $reserve = $this->reservaRepository->findByUuid($id);
        $data = $request->getBodyParams();

        $validator = new Validator($data);
        $rules = [
            'dt_checkin' => 'required',
            'dt_checkout' => 'required',
            'apartament' => 'required',
            'status' => 'required',            
            'amount' => 'required',
            'customers' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->router->redirect('reserva/');
        } 
        
        $data['id_usuario'] = $_SESSION['user']->code;;
        
        $updated = $this->reservaRepository->update($data, $reserve->id);

        if(is_null($updated)) {            
        return $this->router->view('reservate/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('reserva/');
    }

    public function delete(Request $request, $id) {
        $reserve = $this->reservaRepository->findByUuid($id);
        
        if (is_null($reserve)) {
            return $this->router->view('reserva/', ['active' => 'register', 'danger' => true]);
        }

        $reserve = $this->reservaRepository->delete($reserve->id);

        return $this->router->redirect('reserva/');
    }

    public function findAvailableApartments(Request $request) 
    {
        $data = $request->getBodyParams();
        
        $checkin = $data['dt_checkin'];
        $checkout = $data['dt_checkout'];
        $reservaId = isset($data['reserve']) ? $data['reserve'] : null;

        $errors = [];

        // Verifica se o check-in está presente e é uma data válida
        if (empty($checkin) || !strtotime($checkin)) {
            $errors['dt_checkin'] = 'A data de check-in é obrigatória e deve ser uma data válida.';
        }
    
        // Verifica se o check-out está presente e é uma data válida
        if (empty($checkout) || !strtotime($checkout)) {
            $errors['dt_checkout'] = 'A data de check-out é obrigatória e deve ser uma data válida.';
        }
    
        // Verifica se a data de check-out é posterior à data de check-in
        if (!empty($checkin) && !empty($checkout) && strtotime($checkout) < strtotime($checkin)) {
            $errors['dt_checkout'] = 'A data de check-out deve ser posterior à data de check-in.';
        }
    
        // Verifica se o reservaId é um número inteiro, caso esteja presente
        if ($reservaId !== '' && !filter_var($reservaId, FILTER_VALIDATE_INT)) {
            $errors['reserva_id'] = 'O ID da reserva deve ser um número inteiro válido.';
        }
    
        // Se houver erros de validação, retorna os erros
        if (!empty($errors)) {
            echo json_encode(['errors' => $errors]);
            return;
        }

        $apartamentosDisponiveis = $this->apartamentoRepository->findAvailableApartments($checkin, $checkout);

        if ($reservaId !== '') {
            $apartament =  $this->apartamentoRepository->findApartmentByIdReserve($reservaId);

            if (!is_null($apartament)) {
                $apartamentosDisponiveis[] = $apartament;
            }
        }
        
        echo json_encode($apartamentosDisponiveis);
        exit;
    }

    public function checkin(Request $request) 
    {
        $reservas = $this->reservaRepository->allCheckin();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($reservas, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'reservas' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('reservate/checkin', ['active' => 'register', 'data' => $data]);
    }

    public function executeCkeckin(Request $request, string $id) 
    {
        $reserve = $this->reservaRepository->findByUuid($id);
        
        if (is_null($reserve)) {
            echo json_encode(["status" => 422, "message" => "not found reserve!"]);
        }

        $reserve->status = 'Hospedada';

        $updated = $this->reservaRepository->updateToCheckin($reserve, $reserve->id);

        if (is_null($updated)) {
            echo json_encode(["status" => 422, "message" => "not Sucessfully updated"]);
        }

        echo json_encode(["status" => 200, "message" => "Sucessfully updated"]);
        exit;
    }

    public function checkout(Request $request) 
    {

    }

    public function maps() {
        return $this->router->view('reservate/maps', ['active' => 'register']);
    }

    public function reserve_by_maps(Request $request) {
        $data = $request->getBodyParams();
        $dados = $this->reservaRepository->buscaMapaReservas($data['start'], $data['end']);        
        echo json_encode(
            $dados
        );
        exit;
    }
}