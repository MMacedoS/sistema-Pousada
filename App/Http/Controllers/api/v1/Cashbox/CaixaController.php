<?php

namespace App\Http\Controllers\api\v1\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Transformers\Cashbox\CaixaTransformer;
use App\Transformers\Cashbox\TransacaoCaixaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class CaixaController extends Controller
{
    use GenericTrait, UserToPerson, HasPermissions;

    protected $caixaRepository;
    protected $transacaoCaixaRepository;
    protected $caixaTransformer;
    protected $transacaoCaixaTransformer;

    public function __construct(
        ICaixaRepository $caixaRepository,
        ITransacaoCaixaRepository $transacaoCaixaRepository,
        CaixaTransformer $caixaTransformer,
        TransacaoCaixaTransformer $transacaoCaixaTransformer
    ) {
        $this->caixaRepository = $caixaRepository;
        $this->transacaoCaixaRepository = $transacaoCaixaRepository;
        $this->caixaTransformer = $caixaTransformer;
        $this->transacaoCaixaTransformer = $transacaoCaixaTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('cashbox.view');

        $params = $request->getQueryParams();

        $caixas = $this->caixaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($caixas, $perPage, $currentPage);

        $transformed = $this->caixaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'caixas' => $transformed,
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

        $userId = $this->authUserByApi();

        $data['id_usuario_opened'] = $userId;

        $caixa = $this->caixaRepository->create($data);

        return $this->responseJson([
            'message' => 'Caixa aberto com sucesso',
            'data' => $this->caixaTransformer->transform($caixa)
        ], 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('cashbox.view');

        $caixa = $this->caixaRepository->findByUuid($uuid);

        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        return $this->responseJson([
            'cashbox' => $this->caixaTransformer->transform($caixa)
        ]);
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

        return $this->responseJson([
            'message' => 'Caixa atualizado com sucesso',
            'data' => $this->caixaTransformer->transform($updated)
        ]);
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

        return $this->responseJson([
            'message' => 'Caixa fechado com sucesso',
            'data' => $this->caixaTransformer->transform($fechado)
        ]);
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

        return $this->responseJson([
            'data' => $this->caixaTransformer->transform($caixa)
        ]);
    }

    public function transactions(Request $request, string $caixa_id)
    {
        $caixa = $this->caixaRepository->findByUuid($caixa_id);

        if (!$caixa) {
            return $this->responseJson(['message' => 'Caixa não encontrado'], 404);
        }

        $transacoes = $this->transacaoCaixaRepository->byCaixaId($caixa->id);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($transacoes, $perPage, $currentPage);

        $transacoes = $paginator->getPaginatedItems();
        $transacoesTransformadas = $this->transacaoCaixaTransformer->transformCollection($transacoes);

        $paginationData = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->totalItems(),
            'last_page' => $paginator->lastPage(),
            'has_previous_page' => $paginator->hasPreviousPage(),
            'has_next_page' => $paginator->hasNextPage(),
        ];

        return $this->responseJson([
            'transacoes' => $transacoesTransformadas,
            'pagination' => $paginationData
        ]);
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

        $transacao = $this->transacaoCaixaRepository->create($data);

        return $this->responseJson($transacao, 201);
    }

    public function cancelledTransaction(Request $request, int $id)
    {
        $transacao = $this->transacaoCaixaRepository->cancelledTransaction($id);

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
