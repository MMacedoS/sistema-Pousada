<?php

namespace App\Http\Controllers\v1\User;

use App\Config\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Request\Request;
use App\Repositories\Contracts\File\IArquivoRepository;
use App\Repositories\Contracts\Permission\IPermissaoRepository;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Utils\Paginator;
use App\Utils\Validator;

class UsuarioController extends Controller
{
    use UserToPerson;

    protected $usuarioRepository;
    protected $pessoaFisicaRepository;
    protected $permissaoRepository;
    protected $arquivoRepository;

    public function __construct(
        IUsuarioRepository $usuarioRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IPermissaoRepository $permissaoRepository,
        IArquivoRepository $arquivoRepository
    )
    {   
        parent::__construct();
        $this->usuarioRepository = $usuarioRepository;
        $this->permissaoRepository = $permissaoRepository;
        $this->arquivoRepository = $arquivoRepository;        
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
    }

    public function index(Request $request) {
        if(!hasPermission('visualizar usuários')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $params = $request->getQueryParams();

        $usuario = $this->usuarioRepository->all($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($usuario, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('admin/profile/index', [
            'active' => 'register',
            'usuarios' => $paginatedBoards,
            'links' => $paginator->links(),
            'searchFilter' => $params['name_email'] ?? null,
            'access' => $params['access'] ?? null,
            'situation' => $params['situation'] ?? null
        ]);
    }

    public function create() {
        if(!hasPermission('criar usuários')) {
            return $this->router->redirect('usuario?error=422');
        }

        return $this->router->view('admin/profile/create', ['active' => 'register']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45',           
            'email' => 'required',
            'password' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'usuario/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->usuarioRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('admin/profile/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('usuario/');
    }

    public function edit(Request $request, $id) 
    {
        if(!hasPermission('editar usuarios')) {
            return $this->router->redirect('usuario?error=422');
        }

        $usuario = $this->usuarioRepository->findByUuid($id);
        
        if (is_null($usuario)) {
            return $this->router->view('profile/', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->view('admin/profile/edit', ['active' => 'register', 'usuario' => $usuario]);
    }

    public function update(Request $request, $id) 
    {
        $usuario = $this->usuarioRepository->findByUuid($id);

        if (is_null($usuario)) {
            return $this->router->view('usuario/', ['active' => 'register', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:45',
            'email' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'admin/profile/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $updated = $this->usuarioRepository->update($data, $usuario->id);
        
        if(is_null($updated)) {            
        return $this->router->view('admin/profile/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('usuario/');
    }

    public function profileUpdate(Request $request)
    {
        $personAuth = $this->authUser();
        $data = $request->getBodyParams();

        $updatedPerson = $this->pessoaFisicaRepository->update($data, $personAuth->id);
        $updated = $this->usuarioRepository->update($data, $personAuth->usuario_id);
        
        echo json_encode("sucess!");
        exit();
    } 

    public function profilePasswordUpdate(Request $request)
    {
        $personAuth = $this->authUser();
        $data = $request->getBodyParams();

        $updated = $this->usuarioRepository->updatePassword($data, $personAuth->usuario_id);
        
        $arquivo = $_SESSION['files'];
        return $this->router->view('admin/profile/profile', ['active' => 'register', 'pessoa' => $personAuth, 'arquivo' => $arquivo]);
    } 

    public function delete(Request $request, $id) 
    {
        if(!hasPermission('deletar usuários')) {
            return $this->router->redirect('usuario?error=422');
        }

        $usuario = $this->usuarioRepository->findByUuid($id);
        
        if (is_null($usuario)) {
            return $this->router->view('profile/', ['active' => 'register', 'danger' => true]);
        }

        $usuario = $this->usuarioRepository->delete($usuario->id);

        return $this->router->redirect('usuario/');
    }

    public function login(Request $request) 
    {
        return $this->router->view('admin/login/login');
    }

    public function auth(Request $request) 
    {
        $data = $request->getBodyParams();
        
        $user = $this->usuarioRepository->getLogin($data['email'], $data['password']);
        $auth = new Auth();

        if ($auth->login($user)) {    
            return $this->router->redirect('dashboard/');
        }

        return $this->router->redirect('login/');
    }

    public function logout() {
        $auth = new Auth();
        $auth->logout();
        return $this->router->view('admin/login/login', ['message' => 'Deslogado', 'success' => true]);
    }

    public function permissao(Request $request, $id) {
        $usuario = $this->usuarioRepository->findByUuid($id);
        
        if (is_null($usuario)) {
            return $this->router->view('admin/profile/', ['active' => 'register', 'danger' => true]);
        }

        $permissoes = $this->permissaoRepository->all();
        $permissao = $this->usuarioRepository->findPermissions($usuario->id);

        $data = [
            'usuario' => $usuario, 
            'permissions_user' => $permissao, 
            'permissions' => $permissoes
        ];

        return $this->router->view('admin/profile/permission', ['active' => 'register', 'data' => $data]);
    }

    public function add_permissao(Request $request, $id) 
    {
        $data = $request->getBodyParams();
        $usuario = $this->usuarioRepository->findByUuid($id);
        
        if (is_null($usuario)) {
            return $this->router->view('admin/profile/', ['active' => 'register', 'danger' => true]);
        }

        $permissao = $this->usuarioRepository->addPermissions($data, $usuario->id);
           
        return $this->router->redirect('usuario/'. $id .'/permissao');
    }

    public function profile(Request $request) 
    {                
        $personAuth = $this->authUser();
        $arquivo = $_SESSION['files'];
     
        return $this->router->view('admin/profile/profile', ['active' => 'register', 'pessoa' => $personAuth, 'arquivo' => $arquivo]);
    }

    public function profileUploadPhoto(Request $request) 
    {
        $personAuth = $_SESSION['user'];
        $data = $request->getBodyParams();

        if(isset($_FILES['file'])){
            $data['file'] = $_FILES['file'];
        }
        
        $dir = '/files/profile/';

        $file = $this->usuarioRepository->updatePhoto($data, $dir, $personAuth->code);

        $_SESSION['files'] = $file;
        
        echo json_encode("sucess!");
        exit();
    }
}