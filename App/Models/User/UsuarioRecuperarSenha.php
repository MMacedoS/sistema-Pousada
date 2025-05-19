<?php

namespace App\Models\User;

use App\Models\Traits\UuidTrait;

class UsuarioRecuperarSenha
{
    use UuidTrait;

    public ?int $id;
    public ?string $uuid;
    public ?string $antiga;
    public ?string $nova;
    public ?string $usuario_id;    
    public ?int $ativo;
    public ?string $created_at;
    public ?string $updated_at;


    public function __construct() {}

    public function create(array $data): UsuarioRecuperarSenha
    {
        $usuarioRecuperar = new UsuarioRecuperarSenha();
        if (isset($data['id'])) {
            $usuarioRecuperar->id = $data['id'];
        }
        if (!isset($data['uuid'])) {
            $usuarioRecuperar->uuid = $data['uuid'] ?? $this->generateUUID();
        }
        if (isset($data['old'])) {
            $usuarioRecuperar->antiga = $data['old'];
        }
        if (isset($data['new'])) {
            $usuarioRecuperar->nova = $data['new'];
        }
        if (isset($data['user_id'])) {
            $usuarioRecuperar->usuario_id = $data['user_id'];
        }
        if (isset($data['active'])) {
            $usuarioRecuperar->ativo = $data['active'] ?? null;   
        }  
        if (isset($data['updated_at'])) {
            $usuarioRecuperar->updated_at = $data['updated_at'] ?? null;
        }
        if (isset($data['created_at'])) {
            $usuarioRecuperar->created_at = $data['created_at'] ?? null;
        }
        return $usuarioRecuperar;
    }
}