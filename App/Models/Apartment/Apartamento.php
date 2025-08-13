<?php

namespace App\Models\Apartment;

use App\Models\Traits\UuidTrait;

class Apartamento
{

    use UuidTrait;

    public $id;
    public string $uuid;
    public string $name;
    public ?string $description;
    public $active;
    public ?string $category;
    public $is_deleted;
    public ?string $situation; // Situacao do apartamento: Disponivel, Ocupado, Impedido
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): Apartamento {
        $pessoa_fisica = new Apartamento();
        $pessoa_fisica->id = isset($data['id']) ? $data['id'] : null;
        $pessoa_fisica->uuid = $data['uuid'] ?? $this->generateUUID(); // UUID é gerado se não existir
        $pessoa_fisica->name = isset($data['name']) ? $data['name'] : null;
        $pessoa_fisica->description = isset($data['description']) ? $data['description'] : null;
        $pessoa_fisica->category = isset($data['category']) ? $data['category'] : null;
        $pessoa_fisica->active = isset($data['active']) ? (int)$data['active'] : 1; // Valor padrão 1 se não existir
        $pessoa_fisica->situation = isset($data['situation']) ? $data['situation'] : 'Disponivel'; // Valor padrão 'Disponivel' se não existir
        $pessoa_fisica->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $pessoa_fisica->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;

        return $pessoa_fisica;
    }

    public function update(array $data, Apartamento $apartamento): Apartamento
    {
        $apartamento->name = $data['name'] ?? $apartamento->name;
        $apartamento->description = $data['description'] ?? $apartamento->description;
        $apartamento->category = $data['category'] ?? $apartamento->category;
        $apartamento->situation = $data['situation'] ?? $apartamento->situation;
        $apartamento->active = $data['active'] ?? $apartamento->active;

        return $apartamento;
    }
}
