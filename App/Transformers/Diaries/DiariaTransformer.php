<?php

namespace App\Transformers\Diaries;

class DiariaTransformer
{
    public function transform($diaria)
    {
        return [
            'id' => $diaria->uuid,
            'dt_daily' => $diaria->dt_daily,
            'amount' => $diaria->amount,
            'status' => $diaria->status,
            'id_usuario' => $diaria->id_usuario,
            'id_reserva' => $diaria->id_reserva,
            'is_deleted' => $diaria->is_deleted,
            'created_at' => $diaria->created_at,
            'updated_at' => $diaria->updated_at,
        ];
    }

    public function transformCollection(array $diarias)
    {
        return array_map([$this, 'transform'], $diarias);
    }
}
