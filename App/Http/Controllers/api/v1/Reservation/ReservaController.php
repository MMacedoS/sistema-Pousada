<?php

namespace App\Http\Controllers\api\v1\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Repositories\Contracts\Customer\IClienteRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Reservation\ReservaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class ReservaController extends Controller
{
    protected $reservaRepository;
    protected $reservaTransformer;
    protected $clienteRepository;
    protected $apartamentoRepository;

    public function __construct(
        IReservaRepository $reservaRepository,
        ReservaTransformer $reservaTransformer,
        IClienteRepository $clienteRepository,
        IApartamentoRepository $apartamentoRepository
    ) {
        $this->reservaRepository = $reservaRepository;
        $this->reservaTransformer = $reservaTransformer;
        $this->clienteRepository = $clienteRepository;
        $this->apartamentoRepository = $apartamentoRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission('reservations.view');

        $params = $request->getQueryParams();
        $reservas = $this->reservaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($reservas, $perPage, $currentPage);
        $transformed = $this->reservaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'reservations' => $transformed,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ],
        ]);
    }

    public function storeAll(Request $request)
    {
        $this->checkPermission('reservations.create');

        $data = $request->getJsonBody();

        $validated = new Validator($data);
        $rules = [
            'apartment_ids' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'customer_id' => 'required',
            'status' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ];

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $customer = $this->clienteRepository->findByUuid($data['customer_id']);

        if (is_null($customer)) {
            return $this->responseJson("Customer not found", 404);
        }

        $user_id = $this->authUserByApi();

        if (!empty($data['apartment_ids'])) {
            foreach ($data['apartment_ids'] as $apartmentId) {
                $apartment = $this->apartamentoRepository->findByUuid($apartmentId);
                if (is_null($apartment)) {
                    return $this->responseJson("Apartment not found", 404);
                }

                $reservationData = [
                    'id_apartamento' => $apartment->id,
                    'id_usuario' => $user_id,
                    'customer_id' => $customer->id,
                    'dt_checkin' => $data['check_in'],
                    'dt_checkout' => $data['check_out'],
                    'situation' => $data['status'],
                    'amount' => $data['amount'],
                    'type' => $data['type'],
                    'obs' => $data['obs'] ?? null,
                ];

                $reserva = $this->reservaRepository->create($reservationData);

                if (is_null($reserva)) {
                    return $this->responseJson("Reservation creation failed for apartment ID: $apartmentId", 422);
                }
            }
            return $this->responseJson([
                'message' => 'Reservations created successfully for all apartments.',
            ], 201);
        }

        return $this->responseJson([
            'message' => 'No apartments found for the given criteria.',
        ], 422);
    }

    public function store(Request $request)
    {
        $this->checkPermission('reservations.create');

        $data = $request->getJsonBody();
        $validated = new Validator($data);
        $rules = [
            'apartment_id' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'customer_id' => 'required',
            'status' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ];

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $customer = $this->clienteRepository->findByUuid($data['customer_id']);

        if (is_null($customer)) {
            return $this->responseJson("Customer not found", 404);
        }

        $user_id = $this->authUserByApi();

        $apartment = $this->apartamentoRepository->findByUuid($data['apartment_id']);
        if (is_null($apartment)) {
            return $this->responseJson("Apartment not found", 404);
        }

        $data['id_apartamento'] = $apartment->id;
        $data['id_usuario'] = $user_id;
        $data['customer_id'] = $customer->id;
        $data['dt_checkin'] = $data['check_in'];
        $data['dt_checkout'] = $data['check_out'];
        unset($data['apartment_id'], $data['check_in'], $data['check_out']);

        $reserva = $this->reservaRepository->create($data);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation creation failed", 422);
        }

        $reserva = $this->reservaTransformer->transform($reserva);

        return $this->responseJson([
            'reservation' => $reserva,
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('reservations.edit');

        $data = $request->getJsonBody();
        $validated = new Validator($data);
        $rules = [
            'apartment_id' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'customer_id' => 'required',
            'status' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ];

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $customer = $this->clienteRepository->findByUuid($data['customer_id']);

        if (is_null($customer)) {
            return $this->responseJson("Customer not found", 404);
        }

        $user_id = $this->authUserByApi();

        $apartment = $this->apartamentoRepository->findByUuid($data['apartment_id']);
        if (is_null($apartment)) {
            return $this->responseJson("Apartment not found", 404);
        }

        $data['id_apartamento'] = $apartment->id;
        $data['id_usuario'] = $user_id;
        $data['customer_id'] = $customer->id;
        $data['dt_checkin'] = $data['check_in'];
        $data['dt_checkout'] = $data['check_out'];
        unset($data['apartment_id'], $data['check_in'], $data['check_out']);

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 404);
        }

        $reserva = $this->reservaRepository->update($data, $reserva->id);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation update failed", 422);
        }

        $reserva = $this->reservaTransformer->transform($reserva);

        return $this->responseJson([
            'reservation' => $reserva,
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('reservations.view');

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 404);
        }

        $reserva = $this->reservaTransformer->transform($reserva);
        return $this->responseJson([
            'reservation' => $reserva,
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('reservations.cancel');

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 404);
        }

        $deleted = $this->reservaRepository->delete($reserva->id);

        if (!$deleted) {
            return $this->responseJson("Reservation deletion failed", 422);
        }

        return $this->responseJson([
            'message' => 'Reservation deleted successfully',
        ]);
    }

    public function available(Request $request)
    {
        $this->checkPermission('reservations.view');

        $data = $request->getJsonBody();
        $rules = [
            'check_in' => 'required',
            'check_out' => 'required',
        ];
        $validated = new Validator($data);
        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $apartments = $this->reservaRepository->availableApartments($data);

        return $this->responseJson([
            'apartments' => $apartments
        ]);
    }

    public function changeApartment(Request $request, string $uuid)
    {
        $this->checkPermission('reservations.edit');
        $data = $request->getJsonBody();
        $rules = [
            'id_apartamento' => 'required',
        ];
        $validated = new Validator($data);
        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $reserva = $this->reservaRepository->changeApartment($uuid, $data);
        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found or change failed", 404);
        }

        return $this->responseJson([
            'reservation' => $this->reservaTransformer->transform($reserva)
        ]);
    }

    public function financialReport(Request $request)
    {
        $this->checkPermission('reservations.view');
        $data = $request->getQueryParams();
        $report = $this->reservaRepository->financialReport($data);
        return $this->responseJson(['report' => $report]);
    }

    public function statusReport(Request $request)
    {
        $this->checkPermission('reservations.view');
        $data = $request->getQueryParams();
        $report = $this->reservaRepository->statusReport($data);
        return $this->responseJson(['report' => $report]);
    }

    public function countReport(Request $request)
    {
        $this->checkPermission('reservations.view');
        $data = $request->getQueryParams();
        $report = $this->reservaRepository->countReport($data);
        return $this->responseJson(['report' => $report]);
    }
}
