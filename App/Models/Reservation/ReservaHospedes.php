<?php

namespace App\Models\Reservation;

use App\Models\Traits\UuidTrait;

class ReservaHospedes
{
    public ?int $id_reserva;
    public string $id_hospede;
    public ?int $is_primary;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): ReservaHospedes
    {
        $reserva = new ReservaHospedes();
        $reserva->id_reserva = $data['id_reserva'] ?? null;
        $reserva->id_hospede = $data['id_hospede'];
        $reserva->is_primary = $data['is_primary'] ?? null;
        $reserva->created_at = $data['created_at'] ?? null;
        $reserva->updated_at = $data['updated_at'] ?? null;
        return $reserva;
    }

    public function update(array $data, ReservaHospedes $reserva): ReservaHospedes
    {
        $reserva->id_reserva = $data['id_reserva'] ?? $reserva->id_reserva;
        $reserva->id_hospede = $data['id_hospede'] ?? $reserva->id_hospede;
        $reserva->is_primary = $data['is_primary'] ?? $reserva->is_primary;
        $reserva->created_at = $data['created_at'] ?? $reserva->created_at;
        $reserva->updated_at = $data['updated_at'] ?? $reserva->updated_at;
        return $reserva;
    }
}
