<?php

namespace App\Http\Controllers\api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Models\Reservation\Reserva;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Repositories\Contracts\Daily\IDiariaRepository;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Transformers\Reservation\ReservaTransformer;
use App\Utils\LoggerHelper;
use Monolog\Logger;

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
        IDiariaRepository $diariaRepository,
        ReservaTransformer $reservaTransformer
    ) {
        $this->apartmentRepository = $apartmentRepository;
        $this->reservaRepository = $reservaRepository;
        $this->pagamentoRepository = $pagamentoRepository;
        $this->diariaRepository = $diariaRepository;
        $this->reservaTransformer = $reservaTransformer;
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
        // Garante que a data seja sempre calculada no timezone brasileiro
        $timezone = new \DateTimeZone($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');
        $today = new \DateTime('now', $timezone);
        $todayString = $today->format('Y-m-d');

        LoggerHelper::logInfo("Buscando check-ins para a data: {$todayString} (timezone: {$timezone->getName()})");
        $reservas = $this->reservaRepository->getCheckinToday($todayString);

        if (empty($reservas)) {
            return $this->responseJson([], 200);
        }

        return $this->responseJson($this->reservaTransformer->transformCollection($reservas));
    }

    public function checkoutToday()
    {
        // Garante que a data seja sempre calculada no timezone brasileiro
        $timezone = new \DateTimeZone($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');
        $today = new \DateTime('now', $timezone);
        $todayString = $today->format('Y-m-d');

        LoggerHelper::logInfo("Buscando check-outs para a data: {$todayString} (timezone: {$timezone->getName()})");
        $reservas = $this->reservaRepository->getCheckoutTodayOrLate($todayString);

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
