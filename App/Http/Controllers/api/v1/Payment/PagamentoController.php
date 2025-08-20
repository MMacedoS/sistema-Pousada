<?php

namespace App\Http\Controllers\api\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Transformers\Payment\PagamentoTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class PagamentoController extends Controller
{
    protected $pagamentoRepository;
    protected $vendaRepository;
    protected $caixaRepository;
    protected $pagamentoTransformer;

    public function __construct(
        IPagamentoRepository $pagamentoRepository,
        IVendaRepository $vendaRepository,
        ICaixaRepository $caixaRepository,
        PagamentoTransformer $pagamentoTransformer
    ) {
        $this->pagamentoRepository = $pagamentoRepository;
        $this->vendaRepository = $vendaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->pagamentoTransformer = $pagamentoTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('payments.view');

        $params = $request->getQueryParams();
        $params['status'] = $params['status'] ?? 1;
        $pagamentos = $this->pagamentoRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($pagamentos, $perPage, $currentPage);

        $transformed = $this->pagamentoTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'pagamentos' => $transformed,
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
        $this->checkPermission('payments.create');

        $data = $request->getJsonBody();

        $validator = new Validator($data);
        $rules = [
            'method' => 'required',
            'amount' => 'required',
            'cashbox_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->responseJson(['errors' => $validator->getErrors()], 422);
        }

        $sale = $this->vendaRepository->findByUuid($data['sale_id'] ?? null);
        if ($sale) {
            $data['id_venda'] = $sale->id;
        }

        $cashbox = $this->caixaRepository->findByUuid($data['cashbox_id'] ?? null);
        if ($cashbox) {
            $data['id_caixa'] = $cashbox->id;
        }

        $userId = $this->authUserByApi();
        $data['id_usuario'] = $userId;

        $pagamento = $this->pagamentoRepository->create($data);

        return $this->responseJson($this->pagamentoTransformer->transform($pagamento), 201);
    }

    public function show(Request $request, string $uuid)
    {
        $this->checkPermission('payments.view');

        $pagamento = $this->pagamentoRepository->findByUuid($uuid);

        if (!$pagamento) {
            return $this->responseJson(['error' => 'Pagamento não encontrado'], 404);
        }

        return $this->responseJson([
            'pagamento' => $this->pagamentoTransformer->transform($pagamento)
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('payments.update');

        $pagamento = $this->pagamentoRepository->findByUuid($uuid);

        if (!$pagamento) {
            return $this->responseJson(['error' => 'Pagamento não encontrado'], 404);
        }

        $data = $request->getJsonBody();
        $pagamentoUpdated = $this->pagamentoRepository->update($data, $pagamento->id);

        return $this->responseJson([
            'message' => 'Pagamento atualizado com sucesso',
            'data' => $this->pagamentoTransformer->transform($pagamentoUpdated)
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->checkPermission('payments.delete');

        $pagamento = $this->pagamentoRepository->findByUuid($uuid);

        if (!$pagamento) {
            return $this->responseJson(['error' => 'Pagamento não encontrado'], 404);
        }

        $deleted = $this->pagamentoRepository->delete($pagamento->id);

        if (!$deleted) {
            return $this->responseJson(['error' => 'Erro ao deletar pagamento'], 500);
        }

        return $this->responseJson(['message' => 'Pagamento removido com sucesso']);
    }

    public function cancelPayment(Request $request, string $uuid)
    {
        $this->checkPermission('payments.cancell');

        $pagamento = $this->pagamentoRepository->findByUuid($uuid);

        if (!$pagamento) {
            return $this->responseJson(['error' => 'Pagamento não encontrado'], 404);
        }

        $pagamentoCanceled = $this->pagamentoRepository->cancelPayment($pagamento->id);

        if (!$pagamentoCanceled) {
            return $this->responseJson(['error' => 'Erro ao cancelar pagamento'], 500);
        }

        return $this->responseJson([
            'message' => 'Pagamento cancelado com sucesso'
        ]);
    }

    public function getPaymentsByVenda(Request $request, string $vendaUuid)
    {
        $this->checkPermission('payments.view');

        $venda = $this->vendaRepository->findByUuid($vendaUuid);

        if (!$venda) {
            return $this->responseJson(['error' => 'Venda não encontrada'], 404);
        }

        $pagamentos = $this->pagamentoRepository->findByVenda($venda->id);

        $transformed = $this->pagamentoTransformer->transformCollection($pagamentos);

        return $this->responseJson(['payments' => $transformed]);
    }
}
