<?php

namespace App\Http\Controllers\api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Customer\IClienteRepository;
use App\Transformers\Customer\ClienteTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class ClienteController extends Controller
{
    protected $clienteRepository;
    protected $clienteTransformer;

    public function __construct(
        IClienteRepository $clienteRepository,
        ClienteTransformer $clienteTransformer
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->clienteTransformer = $clienteTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('customers.view');

        $params = $request->getQueryParams();

        $clientes = $this->clienteRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($clientes, $perPage, $currentPage);
        $transformed = $this->clienteTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'customers' => $transformed,
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

    public function store(Request $request)
    {
        $this->checkPermission('customers.create');

        $data = $request->getJsonBody();

        $validated = new Validator($data);

        $rules = [
            'name' => 'required',
            'phone' => 'required'
        ];

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $cliente = $this->clienteRepository->savePersonAndCustomer($data);

        if (is_null($cliente)) {
            return $this->responseJson("Customer creation failed", 422);
        }

        $cliente = $this->clienteTransformer->transform($cliente);

        return $this->responseJson([
            'customer' => $cliente,
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('customers.edit');

        $data = $request->getJsonBody();

        $validated = new Validator($data);

        $rules = [
            'name' => 'required',
            'phone' => 'required'
        ];

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->responseJson("Customer not found", 422);
        }

        $data['pessoa_fisica_id'] = $cliente->pessoa_fisica_id;

        $cliente = $this->clienteRepository->updatePersonCustomer($data, $cliente->id);

        if (is_null($cliente)) {
            return $this->responseJson("Customer update failed", 422);
        }

        $cliente = $this->clienteTransformer->transform($cliente);

        return $this->responseJson([
            'customer' => $cliente,
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('customers.view');

        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->responseJson("Customer not found", 404);
        }

        $cliente = $this->clienteTransformer->transform($cliente);

        return $this->responseJson([
            'customer' => $cliente,
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('customers.delete');

        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->responseJson("Customer not found", 404);
        }

        $deleted = $this->clienteRepository->delete($cliente->id);

        if (!$deleted) {
            return $this->responseJson("Customer deletion failed", 422);
        }

        return $this->responseJson([
            'message' => 'Customer deleted successfully',
        ]);
    }
}
