<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\TransacaoCaixa;

class TransacaoCaixaTransformer
{

    public function transform(TransacaoCaixa $transacao)
    {
        if (!$transacao) {
            return null;
        }

        if (is_array($transacao)) {
            $transacao = (object) $transacao;
        }

        return [
            'code' => (int)$transacao->id,
            'id' => $transacao->uuid,
            'type' => $transacao->type,
            'description' => $transacao->description,
            'payment_form' => $transacao->payment_form,
            'origin' => $transacao->origin,
            'canceled' => (bool)$transacao->canceled,
            'amount' => (float)$transacao->amount ?? 0,
            'created_at' => $transacao->created_at,
        ];
    }

    public function transformCollection(array $transacoes)
    {
        return array_map(fn($transacao) => $this->transform($transacao), $transacoes);
    }
}
