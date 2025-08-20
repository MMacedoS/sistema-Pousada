<?php

namespace App\Http\Controllers\api\v1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Transformers\Sale\ItemVendaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class ItemVendaController extends Controller
{
    protected $itemVendaRepository;
    protected $produtoRepository;
    protected $itemVendaTransformer;
    protected $vendaRepository;

    public function __construct(
        IItemVendaRepository $itemVendaRepository,
        IProdutoRepository $produtoRepository,
        ItemVendaTransformer $itemVendaTransformer,
        IVendaRepository $vendaRepository
    ) {
        $this->itemVendaRepository = $itemVendaRepository;
        $this->produtoRepository = $produtoRepository;
        $this->itemVendaTransformer = $itemVendaTransformer;
        $this->vendaRepository = $vendaRepository;
    }

    public function index(Request $request, string $sale_uuid)
    {
        $this->checkPermission('sales.view');

        $sale = $this->vendaRepository->findByUuid($sale_uuid);

        $params = $request->getQueryParams();

        $params['id_venda'] = $sale->id;

        $items = $this->itemVendaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($items, $perPage, $currentPage);

        $transformed = $this->itemVendaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'itens' => $transformed,
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

    public function store(Request $request, string $sale_uuid)
    {
        $this->checkPermission('sales.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'product_id' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $sale = $this->vendaRepository->findByUuid($sale_uuid);

        if (!$sale) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $product = $this->produtoRepository->findByUuid($data['product_id']);

        if (!$product) {
            return $this->responseJson(['error' => 'Produto não encontrado'], 404);
        }

        $data['id_venda'] = $sale->id;
        $data['product_id'] = $product->id;

        $userId = $this->authUserByApi();
        $data['id_usuario'] = $userId;

        $item = $this->itemVendaRepository->create($data);

        return $this->responseJson($this->itemVendaTransformer->transform($item), 201);
    }

    public function show(Request $request, string $sale_uuid, string $uuid)
    {
        $this->checkPermission('sales.view');

        $item = $this->itemVendaRepository->findByUuid($uuid);

        if (!$item) {
            return $this->responseJson(['error' => 'Item não encontrado'], 404);
        }

        return $this->responseJson([
            'item' => $this->itemVendaTransformer->transform($item)
        ]);
    }

    public function update(Request $request, string $sale_uuid, string $uuid)
    {
        $this->checkPermission('sales.edit');

        $sale = $this->vendaRepository->findByUuid($sale_uuid);

        if (!$sale) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $item = $this->itemVendaRepository->findByUuid($uuid);

        if (!$item) {
            return $this->responseJson(['error' => 'Item não encontrado'], 404);
        }

        $data = $request->getJsonBody();
        $itemUpdated = $this->itemVendaRepository->update($data, $item->id);

        return $this->responseJson([
            'message' => 'Item atualizado com sucesso',
            'data' => $this->itemVendaTransformer->transform($itemUpdated)
        ]);
    }

    public function destroy(Request $request, string $sale_uuid, string $uuid)
    {
        $this->checkPermission('sales.cancel');

        $sale = $this->vendaRepository->findByUuid($sale_uuid);

        if (!$sale) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $item = $this->itemVendaRepository->findByUuid($uuid);

        if (!$item) {
            return $this->responseJson(['error' => 'Item não encontrado'], 404);
        }

        $deleted = $this->itemVendaRepository->delete($item->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao deletar item'], 500);
        }

        return $this->responseJson(['message' => 'Item removido com sucesso']);
    }

    public function updateQuantity(Request $request, string $sale_uuid, string $uuid)
    {
        $this->checkPermission('sales.edit');

        $sale = $this->vendaRepository->findByUuid($sale_uuid);

        if (!$sale) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $item = $this->itemVendaRepository->findByUuid($uuid);

        if (!$item) {
            return $this->responseJson(['error' => 'Item não encontrado'], 404);
        }

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = ['quantity' => 'required'];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $quantity = $data['quantity'] ?? 1;
        $updatedItem = $this->itemVendaRepository->updateQuantity($item->id, $quantity);

        return $this->responseJson([
            'message' => 'Quantidade atualizada com sucesso',
            'data' => $this->itemVendaTransformer->transform($updatedItem)
        ]);
    }
}
