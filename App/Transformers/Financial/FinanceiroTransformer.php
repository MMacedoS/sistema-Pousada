<?php

namespace App\Transformers\Financial;

class FinanceiroTransformer
{
    public function transformDashboard(array $data): array
    {
        return [
            'period' => [
                'start_date' => $data['period']['start_date'] ?? null,
                'end_date' => $data['period']['end_date'] ?? null,
            ],
            'revenue' => $this->transformRevenue($data['revenue'] ?? []),
            'expenses' => $this->transformExpenses($data['expenses'] ?? []),
            'balance' => $this->formatCurrency($data['balance'] ?? 0),
            'payment_methods' => $this->transformPaymentMethods($data['payment_methods'] ?? []),
        ];
    }

    public function transformRevenue(array $data): array
    {
        return [
            'total' => $this->formatCurrency($data['total'] ?? 0),
            'total_raw' => $data['total'] ?? 0,
            'breakdown' => [
                'reservations' => [
                    'amount' => $this->formatCurrency($data['reservations'] ?? 0),
                    'amount_raw' => $data['reservations'] ?? 0,
                    'percentage' => $this->calculatePercentage($data['reservations'] ?? 0, $data['total'] ?? 0),
                ],
                'sales' => [
                    'amount' => $this->formatCurrency($data['sales'] ?? 0),
                    'amount_raw' => $data['sales'] ?? 0,
                    'percentage' => $this->calculatePercentage($data['sales'] ?? 0, $data['total'] ?? 0),
                ],
                'diaries' => [
                    'amount' => $this->formatCurrency($data['diaries'] ?? 0),
                    'amount_raw' => $data['diaries'] ?? 0,
                    'percentage' => $this->calculatePercentage($data['diaries'] ?? 0, $data['total'] ?? 0),
                ],
                'consumptions' => [
                    'amount' => $this->formatCurrency($data['consumptions'] ?? 0),
                    'amount_raw' => $data['consumptions'] ?? 0,
                    'percentage' => $this->calculatePercentage($data['consumptions'] ?? 0, $data['total'] ?? 0),
                ],
            ],
        ];
    }

    public function transformExpenses(array $data): array
    {
        $byForm = $data['by_form'] ?? [];
        $formattedByForm = [];

        foreach ($byForm as $form => $amount) {
            $formattedByForm[$form] = [
                'amount' => $this->formatCurrency($amount),
                'amount_raw' => $amount,
                'percentage' => $this->calculatePercentage($amount, $data['total'] ?? 0),
            ];
        }

        return [
            'total' => $this->formatCurrency($data['total'] ?? 0),
            'total_raw' => $data['total'] ?? 0,
            'withdrawals' => [
                'amount' => $this->formatCurrency($data['withdrawals'] ?? 0),
                'amount_raw' => $data['withdrawals'] ?? 0,
                'percentage' => $this->calculatePercentage($data['withdrawals'] ?? 0, $data['total'] ?? 0),
            ],
            'by_payment_form' => $formattedByForm,
        ];
    }

    public function transformCashFlow(array $data): array
    {
        return [
            'inputs' => [
                'amount' => $this->formatCurrency($data['inputs'] ?? 0),
                'amount_raw' => $data['inputs'] ?? 0,
            ],
            'outputs' => [
                'amount' => $this->formatCurrency($data['outputs'] ?? 0),
                'amount_raw' => $data['outputs'] ?? 0,
            ],
            'withdrawals' => [
                'amount' => $this->formatCurrency($data['withdrawals'] ?? 0),
                'amount_raw' => $data['withdrawals'] ?? 0,
            ],
            'net_flow' => [
                'amount' => $this->formatCurrency($data['net_flow'] ?? 0),
                'amount_raw' => $data['net_flow'] ?? 0,
                'status' => $this->getFlowStatus($data['net_flow'] ?? 0),
            ],
        ];
    }

    public function transformPaymentMethods(array $data): array
    {
        $total = array_sum($data);
        $methods = [];

        foreach ($data as $method => $amount) {
            $methods[$method] = [
                'amount' => $this->formatCurrency($amount),
                'amount_raw' => $amount,
                'percentage' => $this->calculatePercentage($amount, $total),
                'label' => $this->getPaymentMethodLabel($method),
            ];
        }

        return [
            'total' => $this->formatCurrency($total),
            'total_raw' => $total,
            'methods' => $methods,
        ];
    }

    public function transformSummary(array $data): array
    {
        $revenue = $data['revenue'] ?? [];
        $expenses = $data['expenses'] ?? [];

        $totalRevenue = $revenue['total'] ?? 0;
        $totalExpenses = $expenses['total'] ?? 0;
        $balance = $totalRevenue - $totalExpenses;

        return [
            'revenue' => $this->transformRevenue($revenue),
            'expenses' => $this->transformExpenses($expenses),
            'cash_flow' => $this->transformCashFlow($data['cash_flow'] ?? []),
            'payment_methods' => $this->transformPaymentMethods($data['payment_methods'] ?? []),
            'summary' => [
                'total_revenue' => $this->formatCurrency($totalRevenue),
                'total_expenses' => $this->formatCurrency($totalExpenses),
                'balance' => $this->formatCurrency($balance),
                'profit_margin' => $this->calculatePercentage($balance, $totalRevenue),
                'status' => $this->getBalanceStatus($balance),
            ],
        ];
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    private function calculatePercentage(float $partial, float $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($partial / $total) * 100, 2);
    }

    private function getFlowStatus(float $value): string
    {
        if ($value > 0) {
            return 'positive';
        } elseif ($value < 0) {
            return 'negative';
        }

        return 'neutral';
    }

    private function getBalanceStatus(float $balance): string
    {
        if ($balance > 0) {
            return 'profit';
        } elseif ($balance < 0) {
            return 'loss';
        }

        return 'break_even';
    }

    private function getPaymentMethodLabel(string $method): string
    {
        $labels = [
            'cash' => 'Dinheiro',
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'pix' => 'PIX',
            'transfer' => 'Transferência',
            'other' => 'Outros',
        ];

        return $labels[$method] ?? ucfirst($method);
    }
}
