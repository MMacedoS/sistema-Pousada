<?php

namespace App\Http\Controllers\api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Repositories\Contracts\Daily\IDiariaRepository;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Utils\LoggerHelper;

class DashboardController extends Controller
{
    protected $apartmentRepository;
    protected $reservaRepository;
    protected $pagamentoRepository;
    protected $diariaRepository;
    protected $reservaTransformer;

    public function __construct(
        IApartamentoRepository $apartmentRepository,
        IReservaRepository $reservaRepository,
        IPagamentoRepository $pagamentoRepository,
        IDiariaRepository $diariaRepository
    ) {
        $this->apartmentRepository = $apartmentRepository;
        $this->reservaRepository = $reservaRepository;
        $this->pagamentoRepository = $pagamentoRepository;
        $this->diariaRepository = $diariaRepository;
    }

    public function apartmentsStatus()
    {
        $max = $this->apartmentRepository->countAll();
        $occupied = $this->apartmentRepository->countOccupied();

        return $this->responseJson([
            'max' => $max,
            'occupied' => $occupied
        ]);
    }

    public function checkinToday()
    {
        $today = date('Y-m-d');
        $reservas = $this->reservaRepository->getCheckinToday($today);

        if (empty($reservas)) {
            return $this->responseJson([], 200);
        }

        return $this->responseJson($this->reservaTransformer->transformCollection($reservas));
    }

    public function checkoutToday()
    {
        $today = date('Y-m-d');
        $reservas = $this->reservaRepository->getCheckoutTodayOrLate($today);

        if (empty($reservas)) {
            return $this->responseJson([], 200);
        }

        return $this->responseJson($this->reservaTransformer->transformCollection($reservas));
    }

    public function guestsCount()
    {
        $count = $this->reservaRepository->getCurrentGuestsCount();

        return $this->responseJson(['current_guests' => $count]);
    }

    public function dailyRevenue(Request $request)
    {
        $start = $request->getParam('start');
        $end = $request->getParam('end');

        $revenue = $this->pagamentoRepository->getRevenueByPeriod($start, $end);

        return $this->responseJson(['revenue' => $revenue]);
    }
}
