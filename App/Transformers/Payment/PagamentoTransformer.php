<?php

namespace App\Transformers\Payment;

use App\Models\Payment\Pagamento;
use App\Repositories\Entities\Sale\VendaRepository;
use App\Repositories\Entities\Reservation\ReservaRepository;
use App\Transformers\Reservation\ReservaTransformer;
use App\Transformers\Sale\VendaTransformer;

class PagamentoTransformer
{
    public function transform(Pagamento $pagamento)
    {
        $reservaData = null;
        $vendaData = null;

        if (!is_null($pagamento->id_reserva)) {
            $reservaData = $this->getReservaData($pagamento->id_reserva);
        }

        if (!is_null($pagamento->id_venda)) {
            $vendaData = $this->getVendaData($pagamento->id_venda);
        }

        return [
            'id' => $pagamento->uuid ?? null,
            'reservation_id' => $pagamento->id_reserva ?? null,
            'method' => $pagamento->type_payment ?? null,
            'amount' => $pagamento->payment_amount ?? null,
            'payment_date' => $pagamento->dt_payment ?? null,
            'sale_id' => $vendaData['id'] ?? null,
            "reference" => $pagamento->id_venda ? "Venda" : "Hospedagem",
            'sale_name' => $pagamento->venda_nome ?? $vendaData['name'] ?? null,
            'user_name' => $pagamento->usuario_nome ?? null,
            'status' => $pagamento->status === 1 ? "Pago" : "Pendente",
            'cashbox_id' => $pagamento->id_caixa ?? null,
            'reservation_data' => $reservaData,
            'sale_data' => $vendaData,
            'created_at' => $pagamento->created_at ?? null,
            'updated_at' => $pagamento->updated_at ?? null,
        ];
    }

    public function transformCollection(array $data): array
    {
        return array_map(fn($item) => $this->transform($item), $data);
    }

    private function getReservaData(?int $reservaId): ?array
    {
        if (is_null($reservaId)) {
            return null;
        }

        try {
            $reserva = ReservaRepository::getInstance()->findById($reservaId);
            if (is_null($reserva)) {
                return null;
            }

            $reservaTransformer = new ReservaTransformer();
            return $reservaTransformer->transform($reserva);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getVendaData(?int $vendaId): ?array
    {
        if (is_null($vendaId)) {
            return null;
        }

        try {
            $venda = VendaRepository::getInstance()->findById($vendaId);
            if (is_null($venda)) {
                return null;
            }

            $vendaTransformer = new VendaTransformer();
            return $vendaTransformer->transform($venda);
        } catch (\Exception $e) {
            return null;
        }
    }
}
