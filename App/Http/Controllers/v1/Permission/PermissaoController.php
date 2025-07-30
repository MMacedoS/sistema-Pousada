<?php

namespace App\Http\Controllers\v1\Permission;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Permission\IPermissaoRepository;
use App\Utils\Paginator;
use App\Utils\Validator;

class PermissaoController extends Controller
{
    protected $permissaoRepository;

    public function __construct(
        IPermissaoRepository $permissaoRepository
    ) {
        parent::__construct();
        $this->permissaoRepository = $permissaoRepository;
    }

    public function index(Request $request) {
        $permissao = $this->permissaoRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator(
            $permissao, 
            $perPage, 
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'permissao' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('permission/index', ['active' => 'register', 'data' => $data]);
    }

    public function create() {
        return $this->router->view('permission/create', ['active' => 'register']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'permission/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->permissaoRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('permission/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('permissao/');
    }

    public function edit(Request $request, $id) {
        $permission = $this->permissaoRepository->findByUuid($id);
        
        if (is_null($permission)) {
            return $this->router->view('permission/', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->view('permission/edit', ['active' => 'register', 'permissao' => $permission]);
    }

    public function update(Request $request, $id) {
        $usuario = $this->permissaoRepository->findByUuid($id);

        if (is_null($usuario)) {
            return $this->router->view('permission/', ['active' => 'register', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'permission/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $updated = $this->permissaoRepository->update($usuario->id, $data);
        
        if(is_null($updated)) {            
        return $this->router->view('permission/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('permissao/');
    }

    public function delete(Request $request, $id) {
        $usuario = $this->permissaoRepository->findByUuid($id);
        
        if (is_null($usuario)) {
            return $this->router->view('permission/', ['active' => 'register', 'danger' => true]);
        }

        $usuario = $this->permissaoRepository->delete($usuario->id);

        return $this->router->redirect('permissao/');
    }

}