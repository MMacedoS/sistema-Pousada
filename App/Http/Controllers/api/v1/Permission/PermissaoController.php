<?php

namespace App\Http\Controllers\api\v1\Permission;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Request\Request;
use App\Repositories\Contracts\Permission\IPermissaoRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Transformers\Permission\PermissaoTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class PermissaoController extends Controller
{
    protected $permissaoRepository;
    protected $permissaoTransformer;
    protected $usuarioRepository;

    public function __construct(
        IPermissaoRepository $permissaoRepository,
        PermissaoTransformer $permissaoTransformer,
        IUsuarioRepository $usuarioRepository
    ) {
        parent::__construct();
        $this->permissaoRepository = $permissaoRepository;
        $this->permissaoTransformer = $permissaoTransformer;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission('permissions.manage');
        $perPage = $request->getParam('limit') ? (int)$request->getParam('limit') : 2;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;

        $permissao = $this->permissaoRepository->all();
        $paginator = new Paginator($permissao, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $paginationData = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->totalItems(),
            'last_page' => $paginator->lastPage(),
            'has_previous_page' => $paginator->hasPreviousPage(),
            'has_next_page' => $paginator->hasNextPage(),
        ];

        $transformedPermissoes = $this->permissaoTransformer->transformCollection($paginatedBoards);

        return $this->responseJson([
            'permissions' => $transformedPermissoes,
            'pagination' => $paginationData
        ], 200);
    }

    public function indexWithoutPagination(Request $request)
    {
        $this->checkPermission('permissions.manage');
        $permissao = $this->permissaoRepository->all();
        $transformedPermissoes = $this->permissaoTransformer->transformCollection($permissao);
        return $this->responseJson($transformedPermissoes, 200);
    }

    public function store(Request $request)
    {
        $data = $request->getJsonBody();

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

        if (is_null($created)) {
            return $this->responseJson('Failed to create permission', 422);
        }

        return $this->responseJson(
            $this->permissaoTransformer->transform($created),
            201
        );
    }

    public function update(Request $request, $id)
    {
        $permissao = $this->permissaoRepository->findById($id);

        if (is_null($permissao)) {
            return $this->responseJson('Permission not found', 404);
        }

        $data = $request->getJsonBody();

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

        $updated = $this->permissaoRepository->update($permissao->id, $data);

        if (is_null($updated)) {
            return $this->responseJson('Failed to update permission', 422);
        }

        return $this->responseJson(
            $this->permissaoTransformer->transform($updated),
            200
        );
    }

    public function delete(Request $request, $id)
    {
        $permissao = $this->permissaoRepository->findByUuid($id);

        if (is_null($permissao)) {
            return $this->responseJson('Permission not found', 404);
        }

        $deleted = $this->permissaoRepository->delete($permissao->id);

        if (!$deleted) {
            return $this->responseJson('Failed to delete permission', 422);
        }

        return $this->responseJson("deleted successfully", 204);
    }

    public function assignPermissionToUser(Request $request, string $userId)
    {
        $this->checkPermission('permissions.manage');

        $permissionIds = $request->getJsonBody()['permissions'] ?? [];

        if (empty($permissionIds)) {
            return $this->responseJson('No permissions provided', 422);
        }

        $user = $this->usuarioRepository->findByUuid($userId);

        if (is_null($user)) {
            return $this->responseJson('User not found', 422);
        }

        $existingPermissions = $this->permissaoRepository->getPermissionsByUserId($user->id);

        foreach ($existingPermissions as $existingPermission) {
            if (!in_array($existingPermission->id, $permissionIds)) {
                $this->permissaoRepository->removePermission($user->id, $existingPermission->id);
            }
        }

        $permissionAdd = 0;

        foreach ($permissionIds as $permissionId) {
            $permission = $this->permissaoRepository->assignPermission($user->id, $permissionId);
            if (is_null($permission)) {
                continue;
            }

            $permissionAdd++;
        }

        $permissions = $this->permissaoRepository->getPermissionsByUserId($user->id);

        return $this->responseJson(
            [
                'message' => "{$permissionAdd} Permissions assigned successfully",
                'permissions' => $this->permissaoTransformer->transformCollection($permissions),
            ],
            200
        );
    }

    public function permissionsByUser(Request $request, string $uuid)
    {
        $this->checkPermission('permissions.manage');

        $user = $this->usuarioRepository->findByUuid($uuid);
        if (is_null($user)) {
            return $this->responseJson('User not found', 404);
        }

        $permissions = $this->permissaoRepository->getPermissionsByUserId($user->id);

        return $this->responseJson(
            [
                'permissions' => $this->permissaoTransformer->transformCollection($permissions),
            ],
            200
        );
    }
}
