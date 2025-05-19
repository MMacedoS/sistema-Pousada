<?php

namespace App\Models\User;

use App\Models\Traits\UuidTrait;

class Usuario
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?string $code;
    public ?string $nome;
    public ?string $email; 
    public ?string $arquivo_id;
    public ?string $senha;
    public $ativo;
    public ?string $acesso;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data, bool $forceNewPassword = false): Usuario
    {
        $user = new Usuario();
        $user->id = $data['id'] ?? null;
        $user->uuid = $data['uuid'] ?? $this->generateUUID();
        $user->nome = $data['name'];
        $user->email = $data['email'];
        $user->acesso = $data['acesso'];
        $user->ativo = $data['active'] ?? 1;

        $user->senha = $forceNewPassword
            ? $this->generatePassword($data)
            : $data['existing_password'];

        $user->created_at = $data['created_at'] ?? null;
        $user->updated_at = $data['updated_at'] ?? null;

        return $user;
    }

    public function update(array $data, Usuario $usuario, bool $forceNewPassword = false): Usuario
    {
        $usuario->nome = $data['name'] ?? $usuario->nome;
        $usuario->email = $data['email'] ?? $usuario->email;
        $usuario->acesso = $data['acesso'] ?? $usuario->acesso;
        $usuario->ativo = $data['active'] ?? $usuario->ativo;
        $usuario->senha = $data['password'] ?? $usuario->senha;
        $usuario->senha = $forceNewPassword
            ? $this->generatePassword($data)
            : $data['existing_password'];

        return $usuario;
    }

    private function generatePassword(array $data): string
    {
        $password = !empty($data['password']) ? $data['password'] : 'password123';
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
