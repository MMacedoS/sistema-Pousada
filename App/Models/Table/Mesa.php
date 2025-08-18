<?php

namespace App\Models\Table;

use App\Models\Traits\UuidTrait;

class Mesa
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?int $name;
    public ?string $status;
    public ?int $id_venda;
    public $created_at;

    public function __construct() {}

    public function create(array $data): Mesa
    {
        $mesa = new Mesa();
        $mesa->id = $data['id'] ?? null;
        $mesa->uuid = $data['uuid'] ?? $this->generateUUID();
        $mesa->name = $data['name'] ?? null;
        $mesa->status = $data['status'] ?? 'livre';
        $mesa->id_venda = $data['id_venda'] ?? null;
        $mesa->created_at = $data['created_at'] ?? null;

        return $mesa;
    }

    public function update(array $data, Mesa $mesa): Mesa
    {
        $mesa->name = $data['name'] ?? $mesa->name;
        $mesa->status = $data['status'] ?? $mesa->status;
        $mesa->id_venda = $data['id_venda'] ?? $mesa->id_venda;

        return $mesa;
    }
}
