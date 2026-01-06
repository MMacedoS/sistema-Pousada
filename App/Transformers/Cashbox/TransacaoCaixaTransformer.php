<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\TransacaoCaixa;
use App\Repositories\Entities\Sale\VendaRepository;
use App\Repositories\Entities\Reservation\ReservaRepository;
use App\Repositories\Entities\Payment\PagamentoRepository;
use App\Transformers\Sale\VendaTransformer;
use App\Transformers\Reservation\ReservaTransformer;
use App\Transformers\Payment\PagamentoTransformer;

class TransacaoCaixaTransformer
{
    private const FORM_CASH = 'cash';
    private const FORM_CREDIT_CARD = 'credit_card';
    private const FORM_DEBIT_CARD = 'debit_card';
    private const FORM_PIX = 'pix';
    private const TYPE_INPUT = 'entrada';
    private const TYPE_OUTPUT = 'saida';
    private const TYPE_BLOOD = 'sangria';

    public function transform(TransacaoCaixa $transacao)
    {
        if (!$transacao) {
            return null;
        }

        if (is_array($transacao)) {
            $transacao = (object) $transacao;
        }

        $referenceData = $this->getReferenceData($transacao->origin, $transacao->reference_uuid);

        return [
            'code' => (int)$transacao->id,
            'id' => $transacao->uuid,
            'type' => $transacao->type,
            'description' => $transacao->description,
            'payment_form' => $transacao->payment_form,
            'origin' => $transacao->origin,
            'reference_id' => $transacao->reference_uuid,
            'reference_data' => $referenceData,
            'canceled' => (bool)$transacao->canceled,
            'amount' => (float)$transacao->amount ?? 0,
            'created_at' => $transacao->created_at,
        ];
    }

    private function getReferenceData(?string $origin, ?string $referenceId): ?array
    {
        if (is_null($origin) || is_null($referenceId)) {
            return null;
        }

        try {
            switch (strtolower($origin)) {
                case 'venda':
                case 'sale':
                    $venda = VendaRepository::getInstance()->findByUuid($referenceId);
                    if ($venda) {
                        $vendaTransformer = new VendaTransformer();
                        return $vendaTransformer->transform($venda);
                    }
                    break;

                case 'reserva':
                case 'reservation':
                    $reserva = ReservaRepository::getInstance()->findByUuid($referenceId);
                    if ($reserva) {
                        $reservaTransformer = new ReservaTransformer();
                        return $reservaTransformer->transform($reserva);
                    }
                    break;

                case 'pagamento':
                case 'payment':
                    $pagamento = PagamentoRepository::getInstance()->findByUuid($referenceId);
                    if ($pagamento) {
                        $pagamentoTransformer = new PagamentoTransformer();
                        return $pagamentoTransformer->transform($pagamento);
                    }
                    break;

                default:
                    return [
                        'type' => ucfirst($origin),
                        'id' => $referenceId,
                    ];
            }
        } catch (\Exception $e) {
            return [
                'type' => ucfirst($origin),
                'id' => $referenceId,
                'error' => 'Erro ao buscar dados da referência',
            ];
        }

        return null;
    }

    public function transformCollection(array $transacoes)
    {
        return array_map(fn($transacao) => $this->transform($transacao), $transacoes);
    }

    public function transformTransactionToFinance(array $transacoes)
    {
        $entradas = array_filter($transacoes, fn($transacao) => $transacao->type === self::TYPE_INPUT && !$transacao->canceled);
        $saidas = array_filter($transacoes, fn($transacao) => $transacao->type === self::TYPE_OUTPUT && !$transacao->canceled);
        $sangrias = array_filter($transacoes, fn($transacao) => $transacao->type === self::TYPE_BLOOD && !$transacao->canceled);

        $paymentTotals = $this->calculatePaymentTotals($transacoes);

        return [
            'cashbox_transactions' => $this->transformCollection($transacoes),
            'total_entradas' => $this->calculateTotal($entradas),
            'total_saidas' => $this->calculateTotal($saidas),
            'total_sangrias' => $this->calculateTotal($sangrias),
            'saldo_final' => $this->calculateTotal($entradas) - $this->calculateTotal($saidas),
            'payment_methods' => [
                'credit_card' => $paymentTotals['credit_card'],
                'debit_card' => $paymentTotals['debit_card'],
                'money' => $paymentTotals['money'],
                'pix' => $paymentTotals['pix'],
                'others' => $paymentTotals['others'],
            ],
            'credit_card' => $paymentTotals['credit_card'],
            'debit_card' => $paymentTotals['debit_card'],
            'money' => $paymentTotals['money'],
            'pix' => $paymentTotals['pix'],
            'others' => $paymentTotals['others'],
        ];
    }

    private function calculateTotal(array $transacoes): float
    {
        return array_sum(array_map(fn($transacao) => (float)$transacao->amount, $transacoes));
    }

    private function calculatePaymentTotals(array $transacoes): array
    {
        $activeTransactions = array_filter($transacoes, fn($transacao) => !$transacao->canceled);

        return [
            'credit_card' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === self::FORM_CREDIT_CARD && !$transacao->canceled && $transacao->type === self::TYPE_INPUT) ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'debit_card' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === self::FORM_DEBIT_CARD && !$transacao->canceled && $transacao->type === self::TYPE_INPUT) ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'money' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === self::FORM_CASH && !$transacao->canceled && $transacao->type === self::TYPE_INPUT) ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'pix' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === self::FORM_PIX && !$transacao->canceled && $transacao->type === self::TYPE_INPUT) ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'others' => array_sum(array_map(
                fn($transacao) => !in_array($transacao->payment_form, [self::FORM_CREDIT_CARD, self::FORM_DEBIT_CARD, self::FORM_CASH, self::FORM_PIX]) && !$transacao->canceled && $transacao->type === self::TYPE_INPUT ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
        ];
    }
}
