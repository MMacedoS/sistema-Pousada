<?php

namespace App\Http\Controllers\api\v1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Transformers\Sale\ItemVendaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class ItemVendaController extends Controller
{
    protected $itemVendaRepository;
    protected $produtoRepository;
    protected $itemVendaTransformer;

    public function __construct(
        IItemVendaRepository $itemVendaRepository,
        IProdutoRepository $produtoRepository,
        ItemVendaTransformer $itemVendaTransformer
    ) {
        $this->itemVendaRepository = $itemVendaRepository;
        $this->produtoRepository = $produtoRepository;
        $this->itemVendaTransformer = $itemVendaTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('sales.view');

        $params = $request->getQueryParams();
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

    public function store(Request $request)
    {
        $this->checkPermission('sales.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'id_venda' => 'required',
            'id_produto' => 'required',
            'quantity' => 'required',
            'amount_item' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $userId = $this->authUserByApi();
        $data['id_usuario'] = $userId;

        $item = $this->itemVendaRepository->create($data);

        return $this->responseJson($this->itemVendaTransformer->transform($item), 201);
    }

    public function show(Request $request, string $uuid)
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

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('sales.update');

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

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('sales.delete');

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

    public function updateQuantity(Request $request, string $uuid)
    {
        $this->checkPermission('sales.update');

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
