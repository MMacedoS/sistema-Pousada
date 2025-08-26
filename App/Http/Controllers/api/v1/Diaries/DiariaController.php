<?php

namespace App\Http\Controllers\api\v1\Diaries;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Daily\IDiariaRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Diaries\DiariaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class DiariaController extends Controller
{
    protected $diariaRepository;
    protected $diariaTransformer;
    protected $reservaRepository;

    public function __construct(
        IDiariaRepository $diariaRepository,
        DiariaTransformer $diariaTransformer,
        IReservaRepository $reservaRepository
    ) {
        $this->diariaRepository = $diariaRepository;
        $this->diariaTransformer = $diariaTransformer;
        $this->reservaRepository = $reservaRepository;
    }

    public function index(Request $request, string $uuid)
    {
        $this->checkPermission('daily.view');

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $params = $request->getQueryParams();

        $params['id_reserva'] = $reserva->id;
        $params['is_deleted'] = 0;

        $perDiems = $this->diariaRepository->all($params);

        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($perDiems, $perPage, $currentPage);
        $transformed = $this->diariaTransformer->transformCollection($paginator->getPaginatedItems());

        return $this->responseJson([
            'per_diems' => $transformed,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ],
        ]);
    }

    public function store(Request $request, string $uuid)
    {
        $this->checkPermission('daily.edit');

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $data = $request->getJsonBody();
        $rules = [
            'dt_daily' => 'required',
            'amount' => 'required',
        ];

        $validated = new Validator($data);

        if (!$validated->validate($rules)) {
            return $this->responseJson([
                'errors' => $validated->getErrors()
            ], 422);
        }

        $data['id_reserva'] = $reserva->id;
        $data['id_usuario'] = $this->authUserByApi();

        $perDiem = $this->diariaRepository->create($data);

        if (is_null($perDiem)) {
            return $this->responseJson("Per-diem creation failed", 422);
        }

        return $this->responseJson([
            'message' => 'Per-diem created successfully',
            'per_diem' => $perDiem
        ], 201);
    }

    public function destroy(Request $request, string $uuid, string $diariaUuid)
    {
        $this->checkPermission('daily.edit');

        $reserva = $this->reservaRepository->findByUuid($uuid);

        if (is_null($reserva)) {
            return $this->responseJson("Reservation not found", 422);
        }

        $perDiem = $this->diariaRepository->findByUuid($diariaUuid);

        if (is_null($perDiem) || $perDiem->id_reserva !== $reserva->id) {
            return $this->responseJson("Per-diem not found for this reservation", 422);
        }

        $deleted = $this->diariaRepository->delete($perDiem->id);

        if (!$deleted) {
            return $this->responseJson("Per-diem deletion failed", 422);
        }

        return $this->responseJson([
            'message' => 'Per-diem deleted successfully',
        ]);
    }
}
