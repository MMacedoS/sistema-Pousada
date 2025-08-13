<?php

namespace App\Models\Person;

use App\Models\Traits\UuidTrait;

class PessoaFisica
{

    use UuidTrait;

    public $id;
    public string $uuid;
    public string $name;
    public ?string $social_name;
    public string $email;
    public $usuario_id;
    public ?string $address;
    public $active;
    public ?string $doc;
    public ?string $type_doc;
    public ?string $birthday;
    public ?string $phone;
    public $is_deleted;
    public ?string $gender;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): PessoaFisica {
        $pessoa_fisica = new PessoaFisica();
        $pessoa_fisica->id = isset($data['id']) ? $data['id'] : null;
        $pessoa_fisica->uuid = $data['uuid'] ?? $this->generateUUID(); // UUID é gerado se não existir
        $pessoa_fisica->name = isset($data['name']) ? $data['name'] : null;
        $pessoa_fisica->social_name = isset($data['social_name']) ? $data['social_name'] : null;
        $pessoa_fisica->email = isset($data['email']) ? $data['email'] : null;
        $pessoa_fisica->address = isset($data['address']) ? $data['address'] : null;
        $pessoa_fisica->phone = isset($data['phone']) ? $data['phone'] : null;
        $pessoa_fisica->usuario_id = isset($data['usuario_id']) ? $data['usuario_id'] : null;
        $pessoa_fisica->birthday = isset($data['birthday']) ? $data['birthday'] : null;
        $pessoa_fisica->doc = isset($data['doc']) ? $data['doc'] : null;
        $pessoa_fisica->type_doc = isset($data['type_doc']) ? $data['type_doc'] : null;
        $pessoa_fisica->gender = isset($data['gender']) ? $data['gender'] : null;
        $pessoa_fisica->active = isset($data['active']) ? (int)$data['active'] : 1; // Valor padrão 1 se não existir
        $pessoa_fisica->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $pessoa_fisica->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;

        return $pessoa_fisica;
    }

    public function update(array $data, PessoaFisica $pessoaFisica): PessoaFisica
    {
        $pessoaFisica->usuario_id = $data['user_id'] ?? $pessoaFisica->usuario_id;
        $pessoaFisica->name = $data['name'] ?? $pessoaFisica->name;
        $pessoaFisica->social_name = $data['social_name'] ?? $pessoaFisica->social_name;
        $pessoaFisica->email = $data['email'] ?? $pessoaFisica->email;
        $pessoaFisica->phone = $data['phone'] ?? $pessoaFisica->phone;
        $pessoaFisica->gender = $data['gender'] ?? $pessoaFisica->gender;
        $pessoaFisica->doc = $data['doc'] ?? $pessoaFisica->doc;
        $pessoaFisica->type_doc = $data['type_doc'] ?? $pessoaFisica->type_doc;
        $pessoaFisica->active = $data['active'] ?? $pessoaFisica->active;

        return $pessoaFisica;
    }
}
