<?php

namespace App\Http\Controllers\api\v1\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Utils\Paginator;
use App\Utils\Validator;

class TransacaoCaixaController extends Controller
{
    use GenericTrait, UserToPerson, HasPermissions;

    protected $transacaoRepository;
    protected $caixaRepository;

    public function __construct(
        ITransacaoCaixaRepository $transacaoRepository,
        ICaixaRepository $caixaRepository
    ) {
        $this->transacaoRepository = $transacaoRepository;
        $this->caixaRepository = $caixaRepository;
    }

    /**
     * Listar todas as transações do caixa
     */
    public function index(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $params = [];

        // Filtros opcionais
        if ($request->getParam('caixa_id')) {
            $params['caixa_id'] = $request->getParam('caixa_id');
        }

        if ($request->getParam('type')) {
            $params['type'] = $request->getParam('type');
        }

        if ($request->getParam('origin')) {
            $params['origin'] = $request->getParam('origin');
        }

        $transacoes = $this->transacaoRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($transacoes, $perPage, $currentPage);

        return $this->responseJson([
            'transacoes' => $paginator->getPaginatedItems(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ]
        ]);
    }

    /**
     * Buscar transação por UUID
     */
    public function show(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $uuid = $request->getParam('uuid');

        if (!$uuid) {
            return $this->responseJson(['error' => 'UUID não fornecido'], 400);
        }

        $transacao = $this->transacaoRepository->findByUuid($uuid);

        if (!$transacao) {
            return $this->responseJson(['error' => 'Transação não encontrada'], 404);
        }

        return $this->responseJson(['transacao' => $transacao]);
    }

    /**
     * Criar nova transação de caixa
     */
    public function store(Request $request)
    {
        $this->checkPermission('cashbox.transactions');

        $data = $request->getJsonBody();

        $validator = new Validator($data);

        // Validações
        $rules = [
            'caixa_id' => 'required',
            'type' => 'required',
            'origin' => 'required',
            'payment_form' => 'required',
            'amount' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        // Verificar se o caixa existe e está aberto
        $caixa = $this->caixaRepository->findById($data['caixa_id']);

        if (!$caixa) {
            return $this->responseJson(['error' => 'Caixa não encontrado'], 404);
        }

        if ($caixa->status !== 'aberto') {
            return $this->responseJson(['error' => 'Caixa não está aberto para transações'], 400);
        }

        // Adicionar ID do usuário logado
        $data['id_usuario'] = $this->authUserByApi();

        try {
            // Criar a transação
            $transacao = $this->transacaoRepository->create($data);

            return $this->responseJson([
                'message' => 'Transação criada com sucesso',
                'transacao' => $transacao
            ], 201);
        } catch (\Exception $e) {
            return $this->responseJson(['error' => 'Erro ao criar transação: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cancelar uma transação
     */
    public function cancel(Request $request)
    {
        $this->checkPermission('cashbox.transactions');

        $uuid = $request->getParam('uuid');

        if (!$uuid) {
            return $this->responseJson(['error' => 'UUID não fornecido'], 400);
        }

        $transacao = $this->transacaoRepository->findByUuid($uuid);

        if (!$transacao) {
            return $this->responseJson(['error' => 'Transação não encontrada'], 404);
        }

        if ($transacao->canceled == 1) {
            return $this->responseJson(['error' => 'Transação já está cancelada'], 400);
        }

        try {
            // Cancelar a transação
            $this->transacaoRepository->cancelledTransaction($transacao->id);

            return $this->responseJson(['message' => 'Transação cancelada com sucesso']);
        } catch (\Exception $e) {
            return $this->responseJson(['error' => 'Erro ao cancelar transação: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Listar transações por caixa
     */
    public function byCaixa(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $caixaId = $request->getParam('caixa_id');

        if (!$caixaId) {
            return $this->responseJson(['error' => 'ID do caixa não fornecido'], 400);
        }

        $transacoes = $this->transacaoRepository->byCaixaId($caixaId);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($transacoes, $perPage, $currentPage);

        return $this->responseJson([
            'transacoes' => $paginator->getPaginatedItems(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ]
        ]);
    }

    /**
     * Relatório de transações por período
     */
    public function relatorio(Request $request)
    {
        $this->checkPermission('financial.reports');

        $dataInicio = $request->getParam('data_inicio');
        $dataFim = $request->getParam('data_fim');
        $caixaId = $request->getParam('caixa_id');

        if (!$dataInicio || !$dataFim) {
            return $this->responseJson(['error' => 'Período não informado'], 400);
        }

        $params = [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];

        if ($caixaId) {
            $params['caixa_id'] = $caixaId;
        }

        $transacoes = $this->transacaoRepository->all($params);

        // Calcular totalizadores
        $totalEntradas = 0;
        $totalSaidas = 0;
        $totalCanceladas = 0;

        foreach ($transacoes as $transacao) {
            if ($transacao->canceled == 1) {
                $totalCanceladas += $transacao->amount;
            } elseif ($transacao->type === 'entrada') {
                $totalEntradas += $transacao->amount;
            } else {
                $totalSaidas += $transacao->amount;
            }
        }

        return $this->responseJson([
            'transacoes' => $transacoes,
            'resumo' => [
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'total_canceladas' => $totalCanceladas,
                'saldo_periodo' => $totalEntradas - $totalSaidas,
                'total_transacoes' => count($transacoes)
            ]
        ]);
    }
}
