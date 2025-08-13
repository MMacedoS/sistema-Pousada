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

class CaixaController extends Controller
{
    use GenericTrait, UserToPerson, HasPermissions;

    protected $caixaRepository;
    protected $transacaoRepository;

    public function __construct(
        ICaixaRepository $caixaRepository,
        ITransacaoCaixaRepository $transacaoRepository
    ) {
        $this->caixaRepository = $caixaRepository;
        $this->transacaoRepository = $transacaoRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $caixas = $this->caixaRepository->all();

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($caixas, $perPage, $currentPage);

        return $this->responseJson([
            'caixas' => $paginator->getPaginatedItems(),
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

    public function store(Request $request)
    {
        $this->checkPermission('cashbox.open');

        $data = $request->getJsonBody();
        $data['status'] = 'aberto';

        $validator = new Validator($data);

        $rules = [
            'initial_amount' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $userId = $this->authUserByApi();

        $data['id_usuario_opened'] = $userId;

        $caixa = $this->caixaRepository->create($data);

        return $this->responseJson($caixa, 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('cashbox.view');

        $caixa = $this->caixaRepository->findByUuid($uuid);

        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        return $this->responseJson($caixa);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('cashbox.view');

        $caixa = $this->caixaRepository->findByUuid($uuid);
        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        $data = $request->getJsonBody();
        $updated = $this->caixaRepository->update($data, $caixa->id);

        return $this->responseJson($updated);
    }

    public function closed(Request $request, string $uuid)
    {
        $this->checkPermission('cashbox.close');

        $caixa = $this->caixaRepository->findByUuid($uuid);
        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        $data = $request->getJsonBody();
        $data['status'] = 'fechado';

        $fechado = $this->caixaRepository->closedCashbox($caixa->id, $data);

        return $this->responseJson($fechado);
    }

    public function destroy(Request $request, string $uuid)
    {
        $caixa = $this->caixaRepository->findByUuid($uuid);
        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        $deleted = $this->caixaRepository->delete($caixa->id);
        if (!$deleted) {
            return $this->responseJson(['message' => 'Erro ao deletar caixa'], 500);
        }

        return $this->responseJson(['message' => 'Caixa deletado com sucesso']);
    }

    public function openedCashboxByUserId(Request $request, int $id_usuario)
    {
        $caixa = $this->caixaRepository->openedCashbox($id_usuario);

        if (!$caixa) {
            return $this->responseJson(['message' => 'Nenhum caixa aberto para este usuário'], 404);
        }

        return $this->responseJson($caixa);
    }

    public function transactions(Request $request, int $caixa_id)
    {
        $transacoes = $this->transacaoRepository->byCaixaId($caixa_id);
        return $this->responseJson($transacoes);
    }

    public function createTransaction(Request $request)
    {
        $data = $request->getJsonBody();

        $validator = new Validator($data);

        $rules = [
            'caixa_id' => 'required',
            'type' => 'required',
            'origin' => 'required',
            'description' => 'required',
            'payment_form' => 'required',
            'amount' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $transacao = $this->transacaoRepository->create($data);

        return $this->responseJson($transacao, 201);
    }

    public function cancelledTransaction(Request $request, int $id)
    {
        $transacao = $this->transacaoRepository->cancelledTransaction($id);

        if (!$transacao) {
            return $this->responseJson(['message' => 'Erro ao cancelar transação ou já cancelada'], 400);
        }

        return $this->responseJson($transacao);
    }

    public function all()
    {
        return $this->responseJson($this->caixaRepository->all());
    }
}
