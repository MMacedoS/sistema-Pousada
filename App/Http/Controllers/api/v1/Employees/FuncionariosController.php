<?php

namespace App\Http\Controllers\api\v1\Employees;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Utils\Paginator;
use App\Utils\Validator;

class FuncionariosController extends Controller
{
    use GenericTrait, HasPermissions;

    protected $usuarioRepository;

    public function __construct(IUsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission('employees.view');

        $funcionarios = $this->usuarioRepository->all();

        $perPage = $request->getParam('limit') ? (int)$request->getParam('limit') : 2;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;

        $paginator = new Paginator($funcionarios, $perPage, $currentPage);
        $paginatedFuncionarios = $paginator->getPaginatedItems();

        // Adiciona dados estruturados da paginação
        $paginationData = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->totalItems(),
            'last_page' => $paginator->lastPage(),
            'has_previous_page' => $paginator->hasPreviousPage(),
            'has_next_page' => $paginator->hasNextPage(),
        ];

        return $this->responseJson([
            'funcionarios' => $paginatedFuncionarios,
            'pagination' => $paginationData
        ], 200);
    }

    public function store(Request $request)
    {
        $this->checkPermission('employees.create');

        $data = $request->getJsonBody();

        if (!$data) {
            return $this->responseJson(['status' => 400, 'message' => 'Invalid request body'], 400);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'access' => 'required',
            'active' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->getErrors()
            ], 422);
        }

        if ($this->usuarioRepository->findByEmail($data['email'])) {
            return $this->responseJson(['status' => 409, 'message' => 'Email already exists'], 409);
        }

        $funcionario = $this->usuarioRepository->create($data);

        if (!$funcionario) {
            return $this->responseJson(['status' => 500, 'message' => 'Failed to create employee'], 500);
        }

        return $this->responseJson($funcionario, 201);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('employees.edit');

        $data = $request->getJsonBody();
        if (!$data) {
            return $this->responseJson(['status' => 400, 'message' => 'Invalid request body'], 400);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required',
            'email' => 'required',
            'access' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['status' => 422, 'message' => 'Validation failed', 'errors' => $validator->getErrors()], 422);
        }

        $funcionario = $this->usuarioRepository->findByUuid($uuid);
        if (!$funcionario) {
            return $this->responseJson(['status' => 404, 'message' => 'Employee not found'], 404);
        }

        $updatedFuncionario = $this->usuarioRepository->update($data, (int)$funcionario->id);

        if (!$updatedFuncionario) {
            return $this->responseJson(['status' => 500, 'message' => 'Failed to update employee'], 500);
        }

        return $this->responseJson($updatedFuncionario, 200);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('employees.delete');

        $funcionario = $this->usuarioRepository->findByUuid($uuid);
        if (!$funcionario) {
            return $this->responseJson(['status' => 404, 'message' => 'Employee not found'], 404);
        }

        $deletedFuncionario = $this->usuarioRepository->delete((int)$funcionario->id);

        if (!$deletedFuncionario) {
            return $this->responseJson(['status' => 500, 'message' => 'Failed to delete employee'], 500);
        }

        return $this->responseJson(['status' => 200, 'message' => 'Employee deleted successfully'], 200);
    }
}
