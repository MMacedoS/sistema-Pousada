<?php

namespace App\Models\Sale;

use App\Models\Traits\UuidTrait;

class ItemVenda
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?int $id_venda;
    public ?int $id_produto;
    public ?int $status;
    public ?int $quantity;
    public ?float $amount_item;
    public ?int $id_usuario;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): ItemVenda
    {
        $item = new ItemVenda();
        $item->id = $data['id'] ?? null;
        $item->uuid = $data['uuid'] ?? $this->generateUUID();
        $item->id_venda = $data['id_venda'] ?? null;
        $item->id_produto = $data['product_id'] ?? null;
        $item->status = $data['status'] ?? 1;
        $item->quantity = $data['quantity'] ?? 1;
        $item->amount_item = $data['unit_price'] ?? 0.00;
        $item->id_usuario = $data['id_usuario'] ?? null;
        $item->created_at = $data['created_at'] ?? null;
        $item->updated_at = $data['updated_at'] ?? null;

        return $item;
    }

    public function update(array $data, ItemVenda $item): ItemVenda
    {
        $item->id_venda = $data['id_venda'] ?? $item->id_venda;
        $item->id_produto = $data['id_produto'] ?? $item->id_produto;
        $item->status = $data['status'] ?? $item->status;
        $item->quantity = $data['quantity'] ?? $item->quantity;
        $item->amount_item = $data['amount_item'] ?? $item->amount_item;
        $item->id_usuario = $data['id_usuario'] ?? $item->id_usuario;

        return $item;
    }
}
