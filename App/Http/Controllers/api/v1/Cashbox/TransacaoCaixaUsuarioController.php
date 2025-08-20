<?php

namespace App\Http\Controllers\api\v1\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Transformers\Cashbox\TransacaoCaixaTransformer;
use App\Utils\Paginator;

class TransacaoCaixaUsuarioController extends Controller
{
    protected $usuarioRepository;
    protected $transacaoCaixaRepository;
    protected $caixaRepository;
    protected $transacaoCaixaTransformer;

    public function __construct(
        IUsuarioRepository $usuarioRepository,
        ITransacaoCaixaRepository $transacaoCaixaRepository,
        ICaixaRepository $caixaRepository,
        TransacaoCaixaTransformer $transacaoCaixaTransformer
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->transacaoCaixaRepository = $transacaoCaixaRepository;
        $this->caixaRepository = $caixaRepository;
        $this->transacaoCaixaTransformer = $transacaoCaixaTransformer;
    }

    public function index(Request $request, string $uuid)
    {
        $this->checkPermission('cashbox.view');

        $user = $this->usuarioRepository->findByUuid($uuid);

        if (!$user) {
            return $this->responseJson(['error' => 'Usuário não encontrado'], 404);
        }

        $params = $request->getQueryParams();
        $params['id_usuario'] = $user->id;

        $transacoes = $this->transacaoCaixaRepository->all($params);

        if (is_null($transacoes)) {
            return $this->responseJson(['error' => 'Nenhuma transação encontrada para o usuário'], 404);
        }

        $transacoes = $this->transacaoCaixaTransformer->transformCollection($transacoes);

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
}
