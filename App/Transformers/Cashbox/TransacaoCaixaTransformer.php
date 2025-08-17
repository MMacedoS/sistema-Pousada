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

    public function transformTransactionToFinance(array $transacoes)
    {
        $entradas = array_filter($transacoes, fn($transacao) => $transacao->type === 'entrada' && !$transacao->canceled);
        $saidas = array_filter($transacoes, fn($transacao) => $transacao->type === 'saida' && !$transacao->canceled);
        $sangrias = array_filter($transacoes, fn($transacao) => $transacao->origin === 'sangria' && !$transacao->canceled);

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
                fn($transacao) => ($transacao->payment_form === 'Cartão de Crédito' && !$transacao->canceled && $transacao->type === 'entrada') ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'debit_card' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === 'Cartão de Débito' && !$transacao->canceled && $transacao->type === 'entrada') ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'money' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === 'Dinheiro' && !$transacao->canceled && $transacao->type === 'entrada') ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'pix' => array_sum(array_map(
                fn($transacao) => ($transacao->payment_form === 'PIX' && !$transacao->canceled && $transacao->type === 'entrada') ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
            'others' => array_sum(array_map(
                fn($transacao) => !in_array($transacao->payment_form, ['Cartão de Crédito', 'Cartão de Débito', 'Dinheiro', 'PIX']) && !$transacao->canceled && $transacao->type === 'entrada' ? (float)$transacao->amount : 0,
                $activeTransactions
            )),
        ];
    }
}
