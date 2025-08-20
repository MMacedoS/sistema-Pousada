<?php

namespace App\Models\Product;

use App\Models\Traits\UuidTrait;

class Produto
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?string $name;
    public ?string $description;
    public ?string $price;
    public ?string $category;
    public ?string $stock;
    public ?string $status;
    public ?string $id_usuario;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): Produto
    {
        $produto = new Produto();
        $produto->id = $data['id'] ?? null;
        $produto->uuid = $data['uuid'] ?? $this->generateUUID();
        $produto->name = $data['name'] ?? null;
        $produto->description = $data['description'] ?? null;
        $produto->price = $data['price'] ?? 0.00;
        $produto->category = $data['category'] ?? null;
        $produto->stock = $data['stock_quantity'] ?? null;
        $produto->status = $data['status'] ?? 1;
        $produto->id_usuario = $data['id_usuario'] ?? null;
        $produto->created_at = $data['created_at'] ?? null;
        $produto->updated_at = $data['updated_at'] ?? null;

        return $produto;
    }

    public function update(array $data, Produto $produto): Produto
    {
        $produto->name = $data['name'] ?? $produto->name;
        $produto->description = $data['description'] ?? $produto->description;
        $produto->price = $data['price'] ?? $produto->price;
        $produto->category = $data['category'] ?? $produto->category;
        $produto->stock = $data['stock_quantity'] ?? $produto->stock;
        $produto->status = $data['status'] ?? $produto->status;
        $produto->id_usuario = $data['id_usuario'] ?? $produto->id_usuario;

        return $produto;
    }
}
