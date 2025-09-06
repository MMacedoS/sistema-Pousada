<?php

namespace App\Transformers\Reservation;

use App\Models\Reservation\Reserva;
use App\Repositories\Entities\Apartments\ApartamentoRepository;
use App\Repositories\Entities\Consumption\ConsumoRepository;
use App\Repositories\Entities\Daily\DiariaRepository;
use App\Repositories\Entities\Payment\PagamentoRepository;
use App\Repositories\Entities\Reservation\ReservaHospedeRepository;
use App\Repositories\Entities\User\UsuarioRepository;
use App\Transformers\Apartment\ApartmentTransformer;

class ReservaTransformer
{
    public function transform(Reserva $data): array
    {
        return [
            'id' => $data->uuid ?? null,
            'code' => $data->id ?? null,
            'apartment' => $this->prepareApartment($data->id_apartamento),
            'user' => $this->prepareUser($data->id_usuario),
            'customer' => $this->prepareCustomer($data->id),
            'checkin' => $data->dt_checkin ?? null,
            'checkout' => $data->dt_checkout ?? null,
            'situation' => $data->situation ?? null,
            'amount' => $data->amount ?? null,
            'guests' => $data->guest ?? null,
            'estimated_value' => $this->prepareTotalPerDiems($data->id) ?? null,
            'consumption_value' => $this->prepareTotalConsumptions($data->id) ?? null,
            'paid_amount' => $this->preparePaidAmount($data->id) ?? null,
            'type' => $data->type ?? null,
            'obs' => $data->obs ?? null,
            'created_at' => $data->created_at ?? null,
            'updated_at' => $data->updated_at ?? null,
        ];
    }

    public function transformCollection(array $data): array
    {
        return array_map(fn($item) => $this->transform($item), $data);
    }

    private function prepareTotalPerDiems($id)
    {
        $diariaRepository = DiariaRepository::getInstance();
        return $diariaRepository->totalAmountByReservaId($id);
    }

    public function preparePaidAmount($id)
    {
        $pagamentoRepository = PagamentoRepository::getInstance();
        return $pagamentoRepository->paidAmountByReservaId($id);
    }

    private function prepareApartment($id)
    {
        $apartmentRepository = ApartamentoRepository::getInstance();
        $apartment = $apartmentRepository->findById($id);
        $apartmentTransformer = new ApartmentTransformer();
        return $apartmentTransformer->transform($apartment);
    }

    private function prepareUser($id)
    {
        $userRepository = UsuarioRepository::getInstance();
        $user = $userRepository->findById($id);
        return $user->name ?? null;
    }

    private function prepareCustomer($id)
    {
        $reservaHospedeRepository = ReservaHospedeRepository::getInstance();
        $customer = $reservaHospedeRepository->findByReservaId($id);
        $reservaHospedeTransformer = new ReservaHospedeTransformer();
        return $reservaHospedeTransformer->transformCustomer($customer);
    }

    private function prepareTotalConsumptions($id)
    {
        $consumoRepository = ConsumoRepository::getInstance();
        return $consumoRepository->totalConsumptionsByReservaId($id);
    }
}
