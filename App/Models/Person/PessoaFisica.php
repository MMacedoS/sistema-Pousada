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
    public ?string $email;
    public $usuario_id;
    public ?string $address;
    public ?string $street;
    public ?string $number;
    public ?string $neighborhood;
    public ?string $city;
    public ?string $state;
    public ?string $zipCode;
    public $active;
    public ?string $doc;
    public ?string $type_doc;
    public ?string $birthday;
    public ?string $phone;
    public $is_deleted;
    public ?string $gender;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): PessoaFisica {
        $pessoa_fisica = new PessoaFisica();
        $pessoa_fisica->id = isset($data['id']) ? $data['id'] : null;
        $pessoa_fisica->uuid = $data['uuid'] ?? $this->generateUUID();
        $pessoa_fisica->name = isset($data['name']) ? $data['name'] : null;
        $pessoa_fisica->social_name = isset($data['social_name']) ? $data['social_name'] : null;
        $pessoa_fisica->email = isset($data['email']) ? $data['email'] : null;
        $pessoa_fisica->phone = isset($data['phone']) ? $data['phone'] : null;
        $pessoa_fisica->usuario_id = isset($data['usuario_id']) ? $data['usuario_id'] : null;
        $pessoa_fisica->birthday = isset($data['birthday']) && !empty($data['birthday']) ? $data['birthday'] : date("Y-m-d");
        $pessoa_fisica->doc = isset($data['doc']) && !empty($data['doc']) ? $data['doc'] : (isset($data['cpf']) && !empty($data['cpf']) ? $data['cpf'] : null);
        $pessoa_fisica->type_doc = isset($data['type_doc']) ? $data['type_doc'] : null;
        $pessoa_fisica->gender = isset($data['gender']) ? $data['gender'] : null;

        // Campos de endereço separados
        $pessoa_fisica->street = isset($data['street']) ? $data['street'] : null;
        $pessoa_fisica->number = isset($data['number']) ? $data['number'] : null;
        $pessoa_fisica->neighborhood = isset($data['neighborhood']) ? $data['neighborhood'] : null;
        $pessoa_fisica->city = isset($data['city']) ? $data['city'] : null;
        $pessoa_fisica->state = isset($data['state']) ? $data['state'] : null;
        $pessoa_fisica->zipCode = isset($data['zipCode']) ? $data['zipCode'] : null;

        // Mantém address para retrocompatibilidade
        $pessoa_fisica->address = isset($data['address']) ? $data['address'] : null;

        $pessoa_fisica->active = isset($data['active']) ? (int)$data['active'] : 1;
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
        $pessoaFisica->birthday = $data['birthday'] ?? $pessoaFisica->birthday;
        $pessoaFisica->gender = $data['gender'] ?? $pessoaFisica->gender;

        // Campos de endereço separados
        $pessoaFisica->street = $data['street'] ?? $pessoaFisica->street;
        $pessoaFisica->number = $data['number'] ?? $pessoaFisica->number;
        $pessoaFisica->neighborhood = $data['neighborhood'] ?? $pessoaFisica->neighborhood;
        $pessoaFisica->city = $data['city'] ?? $pessoaFisica->city;
        $pessoaFisica->state = $data['state'] ?? $pessoaFisica->state;
        $pessoaFisica->zipCode = $data['zipCode'] ?? $pessoaFisica->zipCode;

        // Mantém address para retrocompatibilidade
        $pessoaFisica->address = $data['address'] ?? $pessoaFisica->address;

        // Suporta tanto 'doc' quanto 'cpf'
        if (isset($data['doc']) && !empty($data['doc'])) {
            $pessoaFisica->doc = $data['doc'];
        } elseif (isset($data['cpf']) && !empty($data['cpf'])) {
            $pessoaFisica->doc = $data['cpf'];
        }

        $pessoaFisica->type_doc = $data['type_doc'] ?? $pessoaFisica->type_doc;
        $pessoaFisica->active = $data['active'] ?? $pessoaFisica->active;

        return $pessoaFisica;
    }
}
