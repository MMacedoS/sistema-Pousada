<?php

namespace App\Transformers\Product;

use App\Models\Product\Produto;

class ProdutoTransformer
{
    public function transform($produto)
    {
        return [
            'id' => $produto->uuid ?? null,
            'name' => $produto->name ?? null,
            'description' => $produto->description ?? null,
            'price' => $produto->price ?? null,
            'category' => $produto->category ?? null,
            'stock' => $produto->stock ?? null,
            'stock_quantity' => $produto->estoque_quantity ?? null,
            'status' => $produto->status ?? null,
            'created_at' => $produto->created_at ?? null,
            'updated_at' => $produto->updated_at ?? null,
        ];
    }

    public function transformCollection(array $produtos)
    {
        return array_map([$this, 'transform'], $produtos);
    }
}
