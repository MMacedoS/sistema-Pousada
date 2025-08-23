<?php

namespace App\Transformers\Reservation;

use App\Models\Customer\Cliente;
use App\Models\Reservation\Reserva;
use App\Models\Reservation\ReservaHospedes;
use App\Repositories\Entities\Customer\ClienteRepository;
use App\Transformers\Customer\ClienteTransformer;

class ReservaHospedeTransformer
{
    public function transform(ReservaHospedes $data): array
    {
        return [
            'reservation_id' => $data->id_reserva ?? null,
            'hospede_id' => $data->id_hospede ?? null,
            'created_at' => $data->created_at ?? null,
            'updated_at' => $data->updated_at ?? null,
        ];
    }

    public function transformCustomer(ReservaHospedes $data): array
    {
        $hospedeRepository = ClienteRepository::getInstance();
        $hospede = $hospedeRepository->findById($data->id_hospede);
        $clienteTransformer = new ClienteTransformer();

        return $clienteTransformer->transform($hospede);
    }
}
