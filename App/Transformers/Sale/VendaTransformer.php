<?php

namespace App\Transformers\Sale;

use App\Models\Sale\Venda;

class VendaTransformer
{
    public function transform($venda)
    {
        return [
            'id' => $venda->uuid ?? null,
            'name' => $venda->name ?? null,
            'description' => $venda->description ?? null,
            'sale_date' => $venda->dt_sale ?? null,
            'amount' => $venda->amount_sale ?? 0,
            'status' => $venda->status ?? null,
            'reservation_id' => $venda->reserva_uuid ?? null,
            'user_name' => $venda->usuario_nome ?? null,
            'created_at' => $venda->created_at ?? null,
            'updated_at' => $venda->updated_at ?? null,
        ];
    }

    public function transformCollection(array $vendas)
    {
        return array_map([$this, 'transform'], $vendas);
    }
}
