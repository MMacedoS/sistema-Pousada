<?php

namespace App\Http\Controllers\api\v1\Product;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Transformers\Product\ProdutoTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class ProdutoController extends Controller
{
    protected $produtoRepository;
    protected $produtoTransformer;

    public function __construct(
        IProdutoRepository $produtoRepository,
        ProdutoTransformer $produtoTransformer
    ) {
        $this->produtoRepository = $produtoRepository;
        $this->produtoTransformer = $produtoTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('products.view');

        $params = $request->getQueryParams();
        $produtos = $this->produtoRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($produtos, $perPage, $currentPage);

        $transformed = $this->produtoTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'products' => $transformed,
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
        $this->checkPermission('products.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'price' => 'required',
            'category' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson($validator->getErrors(), 422);
        }

        $userId = $this->authUserByApi();
        $data['id_usuario'] = $userId;

        $produto = $this->produtoRepository->create($data);

        if (is_null($produto)) {
            return $this->responseJson('Erro ao criar produto', 422);
        }

        return $this->responseJson($this->produtoTransformer->transform($produto), 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('products.view');

        $produto = $this->produtoRepository->findByUuid($uuid);

        if (!$produto) {
            return $this->responseJson(['error' => 'Produto não encontrado'], 404);
        }

        return $this->responseJson([
            'produto' => $this->produtoTransformer->transform($produto)
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('products.edit');

        $produto = $this->produtoRepository->findByUuid($uuid);

        if (!$produto) {
            return $this->responseJson(['error' => 'Produto não encontrado'], 404);
        }

        $data = $request->getJsonBody();
        $produtoUpdated = $this->produtoRepository->update($data, $produto->id);

        return $this->responseJson([
            'message' => 'Produto atualizado com sucesso',
            'data' => $this->produtoTransformer->transform($produtoUpdated)
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('products.delete');

        $produto = $this->produtoRepository->findByUuid($uuid);

        if (!$produto) {
            return $this->responseJson(['error' => 'Produto não encontrado'], 404);
        }

        $deleted = $this->produtoRepository->delete($produto->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao deletar produto'], 500);
        }

        return $this->responseJson(['message' => 'Produto removido com sucesso']);
    }

    public function getByCategory(Request $request, string $category)
    {
        $this->checkPermission('products.view');

        $produtos = $this->produtoRepository->findByCategory($category);

        $transformed = $this->produtoTransformer->transformCollection($produtos);

        return $this->responseJson(['produtos' => $transformed]);
    }

    public function getAvailableProducts(Request $request)
    {
        $this->checkPermission('products.view');

        $produtos = $this->produtoRepository->findAvailable();

        $transformed = $this->produtoTransformer->transformCollection($produtos);

        return $this->responseJson(['produtos_disponiveis' => $transformed]);
    }

    public function updateStock(Request $request, string $uuid)
    {
        $this->checkPermission('products.update');

        $produto = $this->produtoRepository->findByUuid($uuid);

        if (!$produto) {
            return $this->responseJson(['error' => 'Produto não encontrado'], 404);
        }

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = ['stock' => 'required'];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $stock = $data['stock'] ?? 0;
        $updatedProduct = $this->produtoRepository->updateStock($produto->id, $stock);

        return $this->responseJson([
            'message' => 'Estoque atualizado com sucesso',
            'data' => $this->produtoTransformer->transform($updatedProduct)
        ]);
    }

    public function getCategories(Request $request)
    {
        $this->checkPermission('products.view');

        $categories = $this->produtoRepository->getCategories();

        return $this->responseJson(['categories' => $categories]);
    }
}
