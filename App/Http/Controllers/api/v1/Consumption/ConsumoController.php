<?php

namespace App\Http\Controllers\api\v1\Consumption;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Consumption\IConsumoRepository;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Consumptions\ConsumoTransformer;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class ConsumoController extends Controller
{
    protected $consumoRepository;
    protected $consumoTransformer;
    protected $reservaRepository;
    protected $produtoRepository;

    public function __construct(
        IConsumoRepository $consumoRepository,
        ConsumoTransformer $consumoTransformer,
        IReservaRepository $reservaRepository,
        IProdutoRepository $produtoRepository
    ) {
        $this->consumoRepository = $consumoRepository;
        $this->consumoTransformer = $consumoTransformer;
        $this->reservaRepository = $reservaRepository;
        $this->produtoRepository = $produtoRepository;
    }

    public function index(Request $request, string $reserva_uuid)
    {
        $this->checkPermission('consumption.view');

        $params = $request->getQueryParams();

        $reserva = $this->reservaRepository->findByUuid($reserva_uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $params['id_reserva'] = $reserva->id;

        $consumos = $this->consumoRepository->all($params);
        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($consumos, $perPage, $currentPage);

        $transformed = $this->consumoTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'consumptions' => $transformed,
            'paginations' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ]
        ]);
    }

    public function store(Request $request, String $reserva_uuid)
    {
        $this->checkPermission('consumption.create');

        $data = $request->getBodyParams();


        $this->checkPermission('daily.edit');

        $reserva = $this->reservaRepository->findByUuid($reserva_uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $data = $request->getJsonBody();
        $rules = [
            'dt_consumption' => 'required',
            'amount' => 'required',
            'product_id' => 'required',
            'quantity' => 'required'
        ];

        $validated = new Validator($data);

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $product = $this->produtoRepository->findByUuid($data['product_id']);

        if (is_null($product)) {
            return $this->responseJson("Product not found", 422);
        }

        $data['product_id'] = (int)$product->id;

        $data['id_reserva'] = (int)$reserva->id;
        $data['id_usuario'] = (int)$this->authUserByApi();

        $consumption = $this->consumoRepository->create($data);

        if (is_null($consumption)) {
            return $this->responseJson("Error creating consumption", 500);
        }

        return $this->responseJson($consumption, 201);
    }

    public function destroy(Request $request, string $reserva_uuid, string $consumoUuid)
    {
        $this->checkPermission('consumption.delete');

        $reserva = $this->reservaRepository->findByUuid($reserva_uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $consumo = $this->consumoRepository->findByUuid($consumoUuid);

        if (is_null($consumo) || $consumo->id_reserva !== $reserva->id) {
            return $this->responseJson("Consumption not found", 422);
        }

        $deleted = $this->consumoRepository->delete($consumo->id);

        if (!$deleted) {
            return $this->responseJson("Error deleting consumption", 500);
        }

        return $this->responseJson("deleted successfully!");
    }
}
