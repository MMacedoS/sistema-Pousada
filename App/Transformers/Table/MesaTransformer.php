<?php

namespace App\Transformers\Table;

use App\Models\Table\Mesa;

class MesaTransformer
{
    public function transform($mesa)
    {
        return [
            'id' => $mesa->uuid ?? null,
            'name' => $mesa->name ?? null,
            'status' => $mesa->status ?? null,
            'sale_id' => $mesa->id_venda ?? null,
            'sale_name' => $mesa->venda_nome ?? null,
            'sale_total' => $mesa->venda_total ?? null,
            'created_at' => $mesa->created_at ?? null,
        ];
    }

    public function transformCollection(array $mesas)
    {
        return array_map([$this, 'transform'], $mesas);
    }
}
