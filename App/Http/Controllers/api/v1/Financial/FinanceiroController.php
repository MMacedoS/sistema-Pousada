<?php

namespace App\Http\Controllers\api\v1\Financial;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Repositories\Contracts\Consumption\IConsumoRepository;
use App\Repositories\Contracts\Daily\IDiariaRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Financial\FinanceiroTransformer;
use App\Utils\Validator;

class FinanceiroController extends Controller
{
    protected $pagamentoRepository;
    protected $vendaRepository;
    protected $caixaRepository;
    protected $transacaoCaixaRepository;
    protected $consumoRepository;
    protected $diariaRepository;
    protected $reservaRepository;
    protected $financeiroTransformer;

    public function __construct(
        IPagamentoRepository $pagamentoRepository,
        IVendaRepository $vendaRepository,
        ICaixaRepository $caixaRepository,
        ITransacaoCaixaRepository $transacaoCaixaRepository,
        IConsumoRepository $consumoRepository,
        IDiariaRepository $diariaRepository,
        IReservaRepository $reservaRepository,
        FinanceiroTransformer $financeiroTransformer
    ) {
        $this->pagamentoRepository = $pagamentoRepository;
        $this->vendaRepository = $vendaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->transacaoCaixaRepository = $transacaoCaixaRepository;
        $this->consumoRepository = $consumoRepository;
        $this->diariaRepository = $diariaRepository;
        $this->reservaRepository = $reservaRepository;
        $this->financeiroTransformer = $financeiroTransformer;
    }

    public function dashboard(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $financialData = $this->buildFinancialDashboard($startDate, $endDate);

        $transformed = $this->financeiroTransformer->transformDashboard($financialData);

        return $this->responseJson($transformed);
    }

    public function revenue(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $revenueData = $this->buildRevenueData($startDate, $endDate);

        $transformed = $this->financeiroTransformer->transformRevenue($revenueData);

        return $this->responseJson($transformed);
    }

    public function expenses(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $expensesData = $this->buildExpensesData($startDate, $endDate);

        $transformed = $this->financeiroTransformer->transformExpenses($expensesData);

        return $this->responseJson($transformed);
    }

    public function cashFlow(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $cashFlowData = $this->buildCashFlowData($startDate, $endDate);

        $transformed = $this->financeiroTransformer->transformCashFlow($cashFlowData);

        return $this->responseJson($transformed);
    }

    public function paymentMethods(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $paymentMethodsData = $this->buildPaymentMethodsData($startDate, $endDate);

        $transformed = $this->financeiroTransformer->transformPaymentMethods($paymentMethodsData);

        return $this->responseJson($transformed);
    }

    public function summary(Request $request)
    {
        $params = $request->getQueryParams();

        $validator = new Validator($params);
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $summaryData = [
            'revenue' => $this->buildRevenueData($startDate, $endDate),
            'expenses' => $this->buildExpensesData($startDate, $endDate),
            'cash_flow' => $this->buildCashFlowData($startDate, $endDate),
            'payment_methods' => $this->buildPaymentMethodsData($startDate, $endDate),
        ];

        $transformed = $this->financeiroTransformer->transformSummary($summaryData);

        return $this->responseJson($transformed);
    }

    private function buildFinancialDashboard(string $startDate, string $endDate): array
    {
        $revenue = $this->buildRevenueData($startDate, $endDate);
        $expenses = $this->buildExpensesData($startDate, $endDate);

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'revenue' => $revenue,
            'expenses' => $expenses,
            'balance' => $revenue['total'] - $expenses['total'],
            'payment_methods' => $this->buildPaymentMethodsData($startDate, $endDate),
        ];
    }

    private function buildRevenueData(string $startDate, string $endDate): array
    {
        $payments = $this->pagamentoRepository->findByPeriod($startDate, $endDate);

        $totalPayments = 0;
        $totalReservations = 0;
        $totalSales = 0;
        $totalDiaries = 0;
        $totalConsumptions = 0;

        foreach ($payments as $payment) {
            if ($payment->status == 1) {
                $totalPayments += $payment->payment_amount;

                if ($payment->id_reserva) {
                    $totalReservations += $payment->payment_amount;
                } elseif ($payment->id_venda) {
                    $totalSales += $payment->payment_amount;
                }
            }
        }

        $diaries = $this->diariaRepository->findByPeriod($startDate, $endDate);
        foreach ($diaries as $diary) {
            if ($diary->status == 1) {
                $totalDiaries += $diary->amount;
            }
        }

        $consumptions = $this->consumoRepository->findByPeriod($startDate, $endDate);
        foreach ($consumptions as $consumption) {
            if ($consumption->status == 1) {
                $totalConsumptions += $consumption->amount;
            }
        }

        return [
            'total' => $totalPayments,
            'reservations' => $totalReservations,
            'sales' => $totalSales,
            'diaries' => $totalDiaries,
            'consumptions' => $totalConsumptions,
        ];
    }

    private function buildExpensesData(string $startDate, string $endDate): array
    {
        $transactions = $this->transacaoCaixaRepository->findByPeriodAndType($startDate, $endDate, 'saida');

        $totalExpenses = 0;
        $totalWithdrawals = 0;
        $expensesByForm = [];

        foreach ($transactions as $transaction) {
            if ($transaction->canceled == 0) {
                $totalExpenses += $transaction->amount;

                if ($transaction->type === 'sangria') {
                    $totalWithdrawals += $transaction->amount;
                }

                $form = $transaction->payment_form ?? 'other';
                if (!isset($expensesByForm[$form])) {
                    $expensesByForm[$form] = 0;
                }
                $expensesByForm[$form] += $transaction->amount;
            }
        }

        return [
            'total' => $totalExpenses,
            'withdrawals' => $totalWithdrawals,
            'by_form' => $expensesByForm,
        ];
    }

    private function buildCashFlowData(string $startDate, string $endDate): array
    {
        $transactions = $this->transacaoCaixaRepository->findByPeriod($startDate, $endDate);

        $totalInputs = 0;
        $totalOutputs = 0;
        $totalWithdrawals = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->canceled == 0) {
                switch ($transaction->type) {
                    case 'entrada':
                        $totalInputs += $transaction->amount;
                        break;
                    case 'saida':
                        $totalOutputs += $transaction->amount;
                        break;
                    case 'sangria':
                        $totalWithdrawals += $transaction->amount;
                        break;
                }
            }
        }

        return [
            'inputs' => $totalInputs,
            'outputs' => $totalOutputs,
            'withdrawals' => $totalWithdrawals,
            'net_flow' => $totalInputs - $totalOutputs - $totalWithdrawals,
        ];
    }

    private function buildPaymentMethodsData(string $startDate, string $endDate): array
    {
        $payments = $this->pagamentoRepository->findByPeriod($startDate, $endDate);

        $methodsData = [
            'cash' => 0,
            'credit_card' => 0,
            'debit_card' => 0,
            'pix' => 0,
            'transfer' => 0,
            'other' => 0,
        ];

        foreach ($payments as $payment) {
            if ($payment->status == 1) {
                $method = $payment->type_payment ?? 'other';
                if (isset($methodsData[$method])) {
                    $methodsData[$method] += $payment->payment_amount;
                } else {
                    $methodsData['other'] += $payment->payment_amount;
                }
            }
        }

        return $methodsData;
    }
}
