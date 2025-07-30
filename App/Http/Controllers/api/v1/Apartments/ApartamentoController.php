<?php

namespace App\Http\Controllers\api\v1\Apartments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Request\Request;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Utils\Paginator;
use App\Utils\Validator;

class ApartamentoController extends Controller
{
    use GenericTrait;
    protected $apartamentoRepository;

    public function __construct(IApartamentoRepository $apartamentoRepository)
    {
        $this->apartamentoRepository = $apartamentoRepository;
    }

    public function index(Request $request)
    {
       $apartamentos = $this->apartamentoRepository->all();
        $perPage = $request->getParam('limit') ? (int)$request->getParam('limit') : 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($apartamentos, $perPage, $currentPage);
        $paginatedApartamentos = $paginator->getPaginatedItems();

        $data = [
            'apartamentos' => $paginatedApartamentos,
            'links' => $paginator->links()
        ];

        return $this->responseJson($data, 200);
    }

    public function store(Request $request)
    {
        $data = $request->getJsonBody();
        
        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45',
            'category' => 'required',
            'active' => 'required',
            'description' => 'required',
            'situation' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->getErrors()
            ], 422);
        }

        $apartamento = $this->apartamentoRepository->create($data);

        if (is_null($apartamento)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Failed to create apartment'
            ], 500);
        }

        return $this->responseJson($apartamento, 201);
    }

    public function show(Request $request, string $id)
    {
        $apartamento = $this->apartamentoRepository->findByUuid($id);

        if (is_null($apartamento)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Apartment not found'
            ], 404);
        }

        return $this->responseJson($apartamento, 200);
    }

    public function update(Request $request, string $id)
    {
        $apartamento = $this->apartamentoRepository->findByUuid($id);

        $data = $request->getJsonBody();
        
        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45',
            'category' => 'required',
            'active' => 'required',
            'description' => 'required',
            'situation' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->getErrors()
            ], 422);
        }

        $apartamento = $this->apartamentoRepository->update($data, $apartamento->id);

        if (is_null($apartamento)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Failed to update apartment'
            ], 500);
        }

        return $this->responseJson($apartamento, 200);
    }

    public function destroy(Request $request, string $id)
    {
        $apartamento = $this->apartamentoRepository->findByUuid($id);

        if (is_null($apartamento)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Apartment not found'
            ], 404);
        }

        $deleted = $this->apartamentoRepository->delete($apartamento->id);

        if (is_null($deleted)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Failed to delete apartment'
            ], 500);
        }

        return $this->responseJson([
            'status' => 'success',
            'message' => 'Apartment deleted successfully'
        ], 200);
    }

    public function changeActiveStatus(Request $request, string $id)
    {
        $apartamento = $this->apartamentoRepository->findByUuid($id);

        if (is_null($apartamento)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Apartment not found'
            ], 404);
        }

        $updatedApartment = $this->apartamentoRepository->changeActiveStatus($apartamento->id);

        if (is_null($updatedApartment)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'Failed to change apartment active status'
            ], 500);
        }

        return $this->responseJson($updatedApartment, 200);
    }

    public function available(Request $request)
    {
        $apartamentos = $this->apartamentoRepository->apartmentsAvailable($request->getJsonBody());

        if (empty($apartamentos)) {
            return $this->responseJson([
                'status' => 'error',
                'message' => 'No available apartments found'
            ], 404);
        }

        return $this->responseJson($apartamentos, 200);
    }   
}