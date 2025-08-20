<?php

namespace App\Transformers\Payment;

use App\Models\Payment\Pagamento;

class PagamentoTransformer
{
    public function transform($pagamento)
    {
        return [
            'id' => $pagamento->uuid ?? null,
            'reservation_id' => $pagamento->id_reserva ?? null,
            'method' => $pagamento->type_payment ?? null,
            'amount' => $pagamento->payment_amount ?? null,
            'payment_date' => $pagamento->dt_payment ?? null,
            'sale_id' => $pagamento->id_venda ?? null,
            "reference" => $pagamento->id_venda ? "Venda" : "Hospedagem",
            'sale_name' => $pagamento->venda_nome ?? null,
            'user_name' => $pagamento->usuario_nome ?? null,
            'status' => $pagamento->status === 1 ? "Pago" : "Pendente",
            'cashbox_id' => $pagamento->id_caixa ?? null,
            'created_at' => $pagamento->created_at ?? null,
            'updated_at' => $pagamento->updated_at ?? null,
        ];
    }

    public function transformCollection(array $pagamentos)
    {
        return array_map([$this, 'transform'], $pagamentos);
    }
}
