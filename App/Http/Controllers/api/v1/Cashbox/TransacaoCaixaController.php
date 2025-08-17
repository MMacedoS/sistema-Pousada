<?php

namespace App\Http\Controllers\api\v1\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Transformers\Cashbox\TransacaoCaixaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class TransacaoCaixaController extends Controller
{
    use GenericTrait, UserToPerson, HasPermissions;

    protected $transacaoCaixaRepository;
    protected $caixaRepository;
    protected $transacaoCaixaTransformer;

    public function __construct(
        ITransacaoCaixaRepository $transacaoCaixaRepository,
        ICaixaRepository $caixaRepository,
        TransacaoCaixaTransformer $transacaoCaixaTransformer
    ) {
        $this->transacaoCaixaRepository = $transacaoCaixaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->transacaoCaixaTransformer = $transacaoCaixaTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $params = [];

        if ($request->getParam('caixa_id')) {
            $params['caixa_id'] = $request->getParam('caixa_id');
        }

        if ($request->getParam('type')) {
            $params['type'] = $request->getParam('type');
        }

        if ($request->getParam('origin')) {
            $params['origin'] = $request->getParam('origin');
        }

        $transacoes = $this->transacaoCaixaRepository->all($params);

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

    public function show(Request $request, string $caixaUuid, string $uuid)
    {
        $this->checkPermission('cashbox.view');

        if (!$caixaUuid || !$uuid) {
            return $this->responseJson(['error' => 'UUID não fornecido'], 400);
        }

        $caixa = $this->caixaRepository->findByUuid($caixaUuid);

        if (!$caixa) {
            return $this->responseJson(['error' => 'Caixa não encontrado'], 422);
        }

        $transacao = $this->transacaoCaixaRepository->findByUuid($uuid);

        if (!$transacao) {
            return $this->responseJson(['error' => 'Transação não encontrada'], 422);
        }

        if ($transacao->caixa_id !== $caixa->id) {
            return $this->responseJson(['error' => 'Transação não pertence ao caixa informado'], 422);
        }

        $transacao = $this->transacaoCaixaTransformer->transform($transacao);

        return $this->responseJson(['transacao' => $transacao]);
    }

    public function store(Request $request, string $caixaUuid)
    {
        $this->checkPermission('cashbox.transactions');

        $data = $request->getJsonBody();

        $validator = new Validator($data);

        $rules = [
            'type' => 'required',
            'origin' => 'required',
            'payment_form' => 'required',
            'amount' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $caixa = $this->caixaRepository->findByUuid($caixaUuid);

        if (!$caixa) {
            return $this->responseJson(['error' => 'Caixa não encontrado'], 404);
        }

        if ($caixa->status !== 'aberto') {
            return $this->responseJson(['error' => 'Caixa não está aberto para transações'], 400);
        }

        $data['id_usuario'] = $this->authUserByApi();
        $data['caixa_id'] = $caixa->id;

        try {
            $transacao = $this->transacaoCaixaRepository->create($data);

            return $this->responseJson([
                'message' => 'Transação criada com sucesso',
                'transacao' => $transacao
            ], 201);
        } catch (\Exception $e) {
            return $this->responseJson(['error' => 'Erro ao criar transação: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $caixaUuid, string $id)
    {
        $this->checkPermission('cashbox.transactions');

        $data = $request->getJsonBody();

        $validator = new Validator($data);

        $rules = [
            'type' => 'required',
            'origin' => 'required',
            'payment_form' => 'required',
            'amount' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $caixa = $this->caixaRepository->findByUuid($caixaUuid);

        if (!$caixa) {
            return $this->responseJson(['error' => 'Caixa não encontrado'], 404);
        }

        if ($caixa->status !== 'aberto') {
            return $this->responseJson(['error' => 'Caixa não está aberto para transações'], 400);
        }

        try {
            $transacao = $this->transacaoCaixaRepository->findByUuid($id);

            if (!$transacao) {
                return $this->responseJson(['error' => 'Transação não encontrada'], 404);
            }

            if ($transacao->caixa_id !== $caixa->id) {
                return $this->responseJson(['error' => 'Transação não pertence ao caixa informado'], 422);
            }

            $transacao = $this->transacaoCaixaRepository->update($data, $transacao->id);

            if (!$transacao) {
                return $this->responseJson(['error' => 'Erro ao atualizar transação'], 500);
            }

            $transacao = $this->transacaoCaixaTransformer->transform($transacao);

            return $this->responseJson([
                'message' => 'Transação atualizada com sucesso',
                'transacao' => $transacao
            ]);
        } catch (\Exception $e) {
            return $this->responseJson(['error' => 'Erro ao atualizar transação: ' . $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, string $caixaUuid, string $id)
    {
        $this->checkPermission('cashbox.transactions');

        if (!$caixaUuid || !$id) {
            return $this->responseJson(['error' => 'UUID não fornecido'], 400);
        }

        $caixa = $this->caixaRepository->findByUuid($caixaUuid);

        if (!$caixa) {
            return $this->responseJson(['error' => 'Caixa não encontrado'], 404);
        }

        if ($caixa->status !== 'aberto') {
            return $this->responseJson(['error' => 'Caixa não está aberto para transações'], 400);
        }

        $transacao = $this->transacaoCaixaRepository->findByUuid($id);

        if (!$transacao) {
            return $this->responseJson(['error' => 'Transação não encontrada'], 404);
        }

        if ($transacao->caixa_id !== $caixa->id) {
            return $this->responseJson(['error' => 'Transação não pertence ao caixa informado'], 422);
        }

        if ($transacao->canceled == 1) {
            return $this->responseJson(['error' => 'Transação já está cancelada'], 400);
        }

        try {
            $this->transacaoCaixaRepository->cancelledTransaction($transacao->id);

            return $this->responseJson(['message' => 'Transação cancelada com sucesso']);
        } catch (\Exception $e) {
            return $this->responseJson(['error' => 'Erro ao cancelar transação: ' . $e->getMessage()], 500);
        }
    }

    public function byCaixa(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $caixaId = $request->getParam('caixa_id');

        if (!$caixaId) {
            return $this->responseJson(['error' => 'ID do caixa não fornecido'], 400);
        }

        $transacoes = $this->transacaoCaixaRepository->byCaixaId($caixaId);

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

    public function reportTransactions(Request $request)
    {
        $this->checkPermission('financial.cashbox.reports');

        $transacoes = $this->transacaoCaixaRepository->all();

        $report = $this->transacaoCaixaTransformer->transformTransactionToFinance($transacoes);

        return $this->responseJson(['summary' => $report]);
    }
}
