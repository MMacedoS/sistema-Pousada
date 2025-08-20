<?php

namespace App\Http\Controllers\api\v1\Table;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Table\IMesaRepository;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Transformers\Table\MesaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class MesaController extends Controller
{
    protected $mesaRepository;
    protected $vendaRepository;
    protected $mesaTransformer;

    public function __construct(
        IMesaRepository $mesaRepository,
        IVendaRepository $vendaRepository,
        MesaTransformer $mesaTransformer
    ) {
        $this->mesaRepository = $mesaRepository;
        $this->vendaRepository = $vendaRepository;
        $this->mesaTransformer = $mesaTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('tables.view');

        $params = $request->getQueryParams();
        $mesas = $this->mesaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($mesas, $perPage, $currentPage);

        $transformed = $this->mesaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'mesas' => $transformed,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->checkPermission('tables.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = ['name' => 'required'];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $mesa = $this->mesaRepository->create($data);

        return $this->responseJson($this->mesaTransformer->transform($mesa), 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('tables.view');

        $mesa = $this->mesaRepository->findByUuid($uuid);

        if (!$mesa) {
            return $this->responseJson(['error' => 'Mesa não encontrada'], 404);
        }

        return $this->responseJson([
            'mesa' => $this->mesaTransformer->transform($mesa)
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('tables.update');

        $mesa = $this->mesaRepository->findByUuid($uuid);

        if (!$mesa) {
            return $this->responseJson(['error' => 'Mesa não encontrada'], 404);
        }

        $data = $request->getJsonBody();
        $mesaUpdated = $this->mesaRepository->update($data, $mesa->id);

        return $this->responseJson([
            'message' => 'Mesa atualizada com sucesso',
            'data' => $this->mesaTransformer->transform($mesaUpdated)
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('tables.delete');

        $mesa = $this->mesaRepository->findByUuid($uuid);

        if (!$mesa) {
            return $this->responseJson(['error' => 'Mesa não encontrada'], 404);
        }

        $deleted = $this->mesaRepository->delete($mesa->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao deletar mesa'], 500);
        }

        return $this->responseJson(['message' => 'Mesa removida com sucesso']);
    }

    public function openTable(Request $request, string $uuid)
    {
        $this->checkPermission('tables.operate');

        $mesa = $this->mesaRepository->findByUuid($uuid);

        if (!$mesa) {
            return $this->responseJson(['error' => 'Mesa não encontrada'], 404);
        }

        if ($mesa->status !== 'livre') {
            return $this->responseJson(['error' => 'Mesa não está livre'], 422);
        }

        $data = $request->getJsonBody();
        $userId = $this->authUserByApi();

        $venda = $this->vendaRepository->create([
            'name' => 'Mesa ' . $mesa->name,
            'description' => 'Venda da mesa ' . $mesa->name,
            'amount_sale' => 0.00,
            'id_usuario' => $userId
        ]);

        $mesaUpdated = $this->mesaRepository->update([
            'status' => 'ocupada',
            'id_venda' => $venda->id
        ], $mesa->id);

        return $this->responseJson([
            'message' => 'Mesa aberta com sucesso',
            'data' => $this->mesaTransformer->transform($mesaUpdated)
        ]);
    }

    public function closeTable(Request $request, string $uuid)
    {
        $this->checkPermission('tables.operate');

        $mesa = $this->mesaRepository->findByUuid($uuid);

        if (!$mesa) {
            return $this->responseJson(['error' => 'Mesa não encontrada'], 404);
        }

        if ($mesa->status !== 'ocupada') {
            return $this->responseJson(['error' => 'Mesa não está ocupada'], 422);
        }

        if ($mesa->id_venda) {
            $this->vendaRepository->closeSale($mesa->id_venda);
        }

        $mesaUpdated = $this->mesaRepository->update([
            'status' => 'livre',
            'id_venda' => null
        ], $mesa->id);

        return $this->responseJson([
            'message' => 'Mesa fechada com sucesso',
            'data' => $this->mesaTransformer->transform($mesaUpdated)
        ]);
    }

    public function getAvailableTables(Request $request)
    {
        $this->checkPermission('tables.view');

        $mesas = $this->mesaRepository->getAvailableTables();

        $transformed = $this->mesaTransformer->transformCollection($mesas);

        return $this->responseJson(['mesas_disponiveis' => $transformed]);
    }
}
