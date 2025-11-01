<?php

namespace App\Transformers\Sale;

use App\Models\Sale\ItemVenda;
use App\Models\Sale\Venda;
use App\Repositories\Entities\Payment\PagamentoRepository;
use App\Repositories\Entities\Reservation\ReservaRepository;
use App\Repositories\Entities\Sale\ItemVendaRepository;
use App\Transformers\Reservation\ReservaTransformer;

class VendaTransformer
{
    public function transform($venda)
    {
        return [
            'id' => $venda->uuid ?? null,
            'name' => $venda->name ?? null,
            'description' => $venda->description ?? null,
            'sale_date' => $venda->dt_sale ?? null,
            'current_amount' => $this->calculateAmountItems($venda->id) + ($venda->amount_sale ?? 0),
            'status' => $venda->status ?? null,
            'payment_status' => $this->preparePaymentStatus($venda->id),
            'payment_details' => $this->getPaymentDetails($venda->id),
            'reservation' => $this->getReservationDetails($venda->id_reserva) ?? null,
            'user_name' => $venda->usuario_nome ?? null,
            'created_at' => $venda->created_at ?? null,
            'updated_at' => $venda->updated_at ?? null,
        ];
    }

    private function calculateAmountItems($vendaId)
    {
        $itemVendaRepository = ItemVendaRepository::getInstance();
        $items = $itemVendaRepository->all(['id_venda' => $vendaId]);
        return array_sum(array_column($items, 'amount_item'));
    }

    private function getReservationDetails($reservaId)
    {
        if (is_null($reservaId)) {
            return null;
        }

        $reservaRepository = ReservaRepository::getInstance();
        $reserva = $reservaRepository->findById($reservaId);

        if (is_null($reserva)) {
            return null;
        }

        $reservaTransformer = new ReservaTransformer();

        return $reservaTransformer->transform($reserva);
    }

    public function transformCollection(array $vendas)
    {
        return array_map([$this, 'transform'], $vendas);
    }

    private function preparePaymentStatus($vendaId)
    {
        $pagamentoRepository = PagamentoRepository::getInstance();
        $pagamentos = $pagamentoRepository->findByVenda($vendaId);

        if (empty($pagamentos)) {
            return 'Pendente';
        }

        $status = array_column($pagamentos, 'status');
        if (in_array(0, $status)) {
            return 'Cancelado';
        }

        return 'Pago';
    }

    public function getPaymentDetails($vendaId)
    {
        $pagamentoRepository = PagamentoRepository::getInstance();
        $pagamentos = $pagamentoRepository->findByVenda($vendaId);

        if (is_null($pagamentos) || empty($pagamentos)) {
            return "Nenhum pagamento encontrado.";
        }

        return [
            "payment" => array_sum(array_column($pagamentos, 'payment_amount')),
            "status" => $pagamentos[0]->status ? "Pago" : "Pendente"
        ];
    }
}
