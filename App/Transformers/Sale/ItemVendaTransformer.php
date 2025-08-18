<?php

namespace App\Transformers\Sale;

use App\Models\Sale\ItemVenda;

class ItemVendaTransformer
{
    public function transform($item)
    {
        return [
            'id' => $item->uuid ?? null,
            'sale_id' => $item->id_venda ?? null,
            'product_id' => $item->id_produto ?? null,
            'product_name' => $item->produto_nome ?? null,
            'product_price' => $item->produto_preco ?? null,
            'quantity' => $item->quantity ?? null,
            'amount' => $item->amount_item ?? null,
            'total' => ($item->quantity ?? 0) * ($item->amount_item ?? 0),
            'status' => $item->status ?? null,
            'created_at' => $item->created_at ?? null,
            'updated_at' => $item->updated_at ?? null,
        ];
    }

    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }
}
