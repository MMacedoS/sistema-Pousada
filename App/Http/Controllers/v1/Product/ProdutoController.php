<?php

namespace App\Http\Controllers\v1\Product;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProdutoRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class ProdutoController extends Controller
{
    protected $produtoRepository;

    public function __construct()
    {   
        parent::__construct();
        $this->produtoRepository = new ProdutoRepository();   
    }

    public function index(Request $request) 
    {
        $params = $request->getBodyParams();
        empty($params) ? $params['status'] = 1 : $params;
        $product = $this->produtoRepository->all($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($product, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'products' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('product/index', ['active' => 'register', 'data' => $data]); 
    }

    public function indexJsonWithoutPaginate(Request $request) 
    {
        $data = $request->getBodyParams();

        $product = $this->produtoRepository->all($data); 
        
        echo json_encode($product);
        exit();
    }

    public function create() {
        return $this->router->view('product/create', ['active' => 'register']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();
        
        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'category' => 'required',     
            'amount' => 'required',
            'stock' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'produtos/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = $_SESSION['user']->code;
        
        $created = $this->produtoRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('product/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('produtos/');
    }

    public function edit(Request $request, $id) {
        $product = $this->produtoRepository->findByUuid($id);
        
        if (is_null($product)) {
            return $this->router->view('produtos/', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->view('product/edit', [
            'active' => 'register', 
            'product' => $product
        ]);
    }

    public function update(Request $request, $id) {
        $reserve = $this->produtoRepository->findByUuid($id);
        $data = $request->getBodyParams();

        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'category' => 'required',     
            'amount' => 'required',
            'stock' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->router->redirect('produtos/');
        } 
        
        $data['id_usuario'] = $_SESSION['user']->code;;
        
        $updated = $this->produtoRepository->update($data, $reserve->id);

        if(is_null($updated)) {            
        return $this->router->view('product/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('produtos/');
    }

    public function destroy(Request $request, $id) {

        $product = $this->produtoRepository->findByUuid($id);

        if(is_null($product)) {
            return $this->router->view('product/index', ['active' => 'register', 'danger' => true, 'products' => []]);
        }

        $deleted = $this->produtoRepository->delete($product->id);

        if (!$deleted) {
            return $this->router->view('product/index', ['active' => 'register', 'danger' => true, 'products' => []]);
        }     

        return $this->router->redirect('produtos/');
    }
}