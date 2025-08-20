<?php

namespace App\Http\Controllers\Traits;

use App\Http\Middleware\PermissionMiddleware;

trait HasPermissions
{
    private $permissionMiddleware;

    protected function getPermissionMiddleware()
    {
        if (!$this->permissionMiddleware) {
            $this->permissionMiddleware = new PermissionMiddleware();
        }
        return $this->permissionMiddleware;
    }

    /**
     * Verifica se tem permissão e retorna erro se não tiver
     */
    protected function checkPermission(string $permissionName): void
    {
        $this->getPermissionMiddleware()->requirePermission($permissionName);
    }

    /**
     * Verifica se tem pelo menos uma das permissões
     */
    protected function checkAnyPermission(array $permissions): void
    {
        $middleware = $this->getPermissionMiddleware();

        if (!$middleware->hasAnyPermission($permissions)) {
            $permissionsList = implode(', ', $permissions);
            $middleware->unauthorizedResponse("Você precisa de pelo menos uma das seguintes permissões: {$permissionsList}");
        }
    }

    /**
     * Verifica se tem todas as permissões
     */
    protected function checkAllPermissions(array $permissions): void
    {
        $middleware = $this->getPermissionMiddleware();

        if (!$middleware->hasAllPermissions($permissions)) {
            $permissionsList = implode(', ', $permissions);
            $middleware->unauthorizedResponse("Você precisa de todas as seguintes permissões: {$permissionsList}");
        }
    }

    /**
     * Obtém as permissões do usuário atual
     */
    protected function getCurrentUserPermissions(): array
    {
        return $this->getPermissionMiddleware()->getUserPermissions();
    }

    /**
     * Verifica se tem permissão sem parar execução
     */
    protected function hasPermission(string $permissionName): bool
    {
        return $this->getPermissionMiddleware()->checkPermission($permissionName);
    }
}
