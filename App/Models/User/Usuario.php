<?php

namespace App\Models\User;

use App\Models\Traits\UuidTrait;

class Usuario
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?string $code;
    public ?string $name;
    public ?string $email; 
    public ?string $arquivo_id;
    public ?string $photo;
    public ?string $password;
    public ?string $send_access;
    public ?string $access;
    public $pessoa_fisica;
    public $active;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data, bool $forceNewPassword = false): Usuario
    {
        $user = new Usuario();
        $user->id = $data['id'] ?? null;
        $user->uuid = $data['uuid'] ?? $this->generateUUID();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->access = $data['access'];
        $user->active = $data['active'] ?? 1;

        $user->password = $forceNewPassword
            ? $this->generatePassword($data)
            : $data['existing_password'];

        $user->created_at = $data['created_at'] ?? null;
        $user->updated_at = $data['updated_at'] ?? null;

        return $user;
    }

    public function update(array $data, Usuario $usuario, bool $forceNewPassword = false): Usuario
    {
        $usuario->name = $data['name'] ?? $usuario->name;
        $usuario->email = $data['email'] ?? $usuario->email;
        $usuario->access = $data['access'] ?? $usuario->access;
        $usuario->active = $data['active'] ?? $usuario->active;
        $usuario->password = $data['password'] ?? $usuario->password;
        $usuario->password = $forceNewPassword
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
