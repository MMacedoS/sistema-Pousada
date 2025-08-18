<?php

namespace App\Http\Controllers\api\v1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Transformers\Sale\VendaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class VendaController extends Controller
{
    protected $vendaRepository;
    protected $itemVendaRepository;
    protected $caixaRepository;
    protected $vendaTransformer;

    public function __construct(
        IVendaRepository $vendaRepository,
        IItemVendaRepository $itemVendaRepository,
        ICaixaRepository $caixaRepository,
        VendaTransformer $vendaTransformer
    ) {
        $this->vendaRepository = $vendaRepository;
        $this->itemVendaRepository = $itemVendaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->vendaTransformer = $vendaTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('sales.view');

        $params = $request->getQueryParams();
        $vendas = $this->vendaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($vendas, $perPage, $currentPage);

        $transformed = $this->vendaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'vendas' => $transformed,
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
        $this->checkPermission('sales.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'amount_sale' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $userId = $this->authUserByApi();
        $data['id_usuario'] = $userId;

        $venda = $this->vendaRepository->create($data);

        return $this->responseJson($this->vendaTransformer->transform($venda), 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('sales.view');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        return $this->responseJson([
            'venda' => $this->vendaTransformer->transform($venda)
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('sales.update');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $data = $request->getJsonBody();
        $vendaUpdated = $this->vendaRepository->update($data, $venda->id);

        return $this->responseJson([
            'message' => 'Venda atualizada com sucesso',
            'data' => $this->vendaTransformer->transform($vendaUpdated)
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('sales.delete');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $deleted = $this->vendaRepository->delete($venda->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao deletar venda'], 500);
        }

        return $this->responseJson(['message' => 'Venda removida com sucesso']);
    }

    public function closeSale(Request $request, string $uuid)
    {
        $this->checkPermission('sales.close');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $vendaClosed = $this->vendaRepository->closeSale($venda->id);

        return $this->responseJson([
            'message' => 'Venda fechada com sucesso',
            'data' => $this->vendaTransformer->transform($vendaClosed)
        ]);
    }

    public function cancelSale(Request $request, string $uuid)
    {
        $this->checkPermission('sales.cancel');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $vendaCanceled = $this->vendaRepository->cancelSale($venda->id);

        return $this->responseJson([
            'message' => 'Venda cancelada com sucesso',
            'data' => $this->vendaTransformer->transform($vendaCanceled)
        ]);
    }

    public function addItem(Request $request, string $uuid)
    {
        $this->checkPermission('sales.update');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'id_produto' => 'required',
            'quantity' => 'required',
            'amount_item' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $userId = $this->authUserByApi();
        $data['id_venda'] = $venda->id;
        $data['id_usuario'] = $userId;

        $item = $this->itemVendaRepository->create($data);

        return $this->responseJson([
            'message' => 'Item adicionado à venda com sucesso',
            'data' => $item
        ], 201);
    }

    public function removeItem(Request $request, string $uuid, string $itemUuid)
    {
        $this->checkPermission('sales.update');

        $venda = $this->vendaRepository->findByUuid($uuid);
        $item = $this->itemVendaRepository->findByUuid($itemUuid);

        if (!$venda || !$item) {
            return $this->responseJson(['error' => 'Venda ou item não encontrado'], 404);
        }

        $deleted = $this->itemVendaRepository->delete($item->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao remover item'], 500);
        }

        return $this->responseJson(['message' => 'Item removido da venda com sucesso']);
    }

    public function getSalesReport(Request $request)
    {
        $this->checkPermission('sales.reports');

        $params = $request->getQueryParams();
        $report = $this->vendaRepository->getTotalSales($params);

        return $this->responseJson(['relatorio' => $report]);
    }
}
