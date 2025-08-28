<?php

namespace App\Transformers\Consumptions;

use App\Models\Consumption\Consumo;

class ConsumoTransformer
{
    public function transform(Consumo $consumo)
    {
        return [
            'code' => $consumo->id,
            'id' => $consumo->uuid,
            'reservation_id' => $consumo->id_reserva,
            'product_id' => $consumo->id_produto,
            'amount' => $consumo->amount,
            'quantity' => $consumo->quantity,
            'total' => $this->prepareTotal($consumo),
            'dt_consumption' => $consumo->dt_consumption,
            'status' => $consumo->status,
            'user_id' => $consumo->id_usuario,
            'is_deleted' => $consumo->is_deleted,
            'created_at' => $consumo->created_at,
            'updated_at' => $consumo->updated_at,
        ];
    }

    public function transformCollection(array $consumos)
    {
        return array_map([$this, 'transform'], $consumos);
    }

    private function prepareTotal(Consumo $consumo): float
    {
        return $consumo->amount * $consumo->quantity;
    }
}
