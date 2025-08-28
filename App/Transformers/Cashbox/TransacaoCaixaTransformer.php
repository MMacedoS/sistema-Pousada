<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\TransacaoCaixa;

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
