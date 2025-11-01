<?php

namespace App\Http\Controllers\api\v1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Sale\VendaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class VendaController extends Controller
{
    protected $vendaRepository;
    protected $itemVendaRepository;
    protected $caixaRepository;
    protected $vendaTransformer;
    protected $reservaRepository;

    public function __construct(
        IVendaRepository $vendaRepository,
        IItemVendaRepository $itemVendaRepository,
        ICaixaRepository $caixaRepository,
        VendaTransformer $vendaTransformer,
        IReservaRepository $reservaRepository
    ) {
        $this->vendaRepository = $vendaRepository;
        $this->itemVendaRepository = $itemVendaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->vendaTransformer = $vendaTransformer;
        $this->reservaRepository = $reservaRepository;
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
            'sales' => $transformed,
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
            'sale_name' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        if (isset($data['reservation_id'])) {
            $reserva = $this->reservaRepository->findByUuid($data['reservation_id']);
            if (!is_null($reserva)) {
                $data['reservation_id'] = $reserva->id;
            }
        }

        $userId = $this->authUserByApi();
        $data['id_usuario'] = (int)$userId;

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
        $this->checkPermission('sales.edit');

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
        $this->checkPermission('sale.close');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $vendaClosed = $this->vendaRepository->closeSale($venda->id);

        return $this->responseJson([
            'message' => 'Venda fechada com sucesso',
        ]);
    }

    public function cancelSale(Request $request, string $uuid)
    {
        $this->checkPermission('sales.cancel');

        $venda = $this->vendaRepository->findByUuid($uuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $this->vendaRepository->cancelSale($venda->id);

        return $this->responseJson([
            'message' => 'Venda cancelada com sucesso',
        ]);
    }

    public function getSalesReport(Request $request)
    {
        $this->checkPermission('sales.reports');

        $params = $request->getQueryParams();
        $report = $this->vendaRepository->getTotalSales($params);

        return $this->responseJson(['relatorio' => $report]);
    }
}
