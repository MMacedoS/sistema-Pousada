<?php

namespace App\Http\Middleware;

use App\Repositories\Entities\Permission\PermissaoRepository;
use App\Repositories\Entities\User\UsuarioRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PermissionMiddleware
{
    private $permissaoRepository;
    private $usuarioRepository;

    public function __construct()
    {
        $this->permissaoRepository = new PermissaoRepository();
        $this->usuarioRepository = new UsuarioRepository();
    }

    public function checkPermission(string $permissionName): bool
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return false;
        }

        $permissions = $this->permissaoRepository->allByUser((int)$user->code);
        foreach ($permissions as $permission) {
            if ($permission->name === $permissionName) {
                return true;
            }
        }

        return false;
    }

    private function getUserFromToken()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));

            if (!isset($decoded->sub)) {
                return null;
            }
            $user = $this->usuarioRepository->findByIdWithPhoto((int)$decoded->sub);
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function unauthorizedResponse(string $message = 'Acesso negado: permissão insuficiente'): void
    {
        http_response_code(403);
        echo json_encode([
            'status' => 403,
            'message' => $message,
            'error' => 'Forbidden'
        ]);
        exit;
    }

    public function unauthenticatedResponse(string $message = 'Token inválido ou expirado'): void
    {
        http_response_code(401);
        echo json_encode([
            'status' => 401,
            'message' => $message,
            'error' => 'Unauthorized'
        ]);
        exit;
    }

    public function requirePermission(string $permissionName): void
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            $this->unauthenticatedResponse();
        }

        if (!$this->checkPermission($permissionName)) {
            $this->unauthorizedResponse("Você não possui permissão para: {$permissionName}");
        }
    }

    public function getUserPermissions(): array
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return [];
        }

        $permissions = $this->permissaoRepository->allByUser((int)$user->code);

        return array_map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description ?? null
            ];
        }, $permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}
