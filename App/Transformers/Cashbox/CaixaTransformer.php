<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\Caixa;
use App\Transformers\BaseTransformer;

class CaixaTransformer
{

    public function transform($caixa)
    {
        if (!$caixa) {
            return null;
        }

        // Convert to object if it's an array
        if (is_array($caixa)) {
            $caixa = (object) $caixa;
        }

        return [
            'code' => (int)$caixa->id,
            'id' => $caixa->uuid,
            'opened_at' => $caixa->opened_at,
            'closed_at' => $caixa->closed_at,
            'initial_amount' => $caixa->initial_amount ?? 0,
            'current_balance' => $caixa->current_balance ?? 0,
            'final_amount' => $caixa->final_amount ?? 0,
            'difference' => $caixa->difference ?? 0,
            'name' => $caixa->name ?? null,
            'transactions' => json_decode($caixa->transactions ?? []),
            'status' => $caixa->status,
            'obs' => $caixa->obs ?? null
        ];
    }

    public function transformCollection(array $caixas)
    {
        return array_map(fn($caixa) => $this->transform($caixa), $caixas);
    }
}
