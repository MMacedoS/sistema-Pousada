<?php

namespace App\Models\Reservation;

use App\Models\Traits\UuidTrait;

class Reserva
{
    use UuidTrait;

    public ?int $id;
    public string $uuid;
    public int $id_apartamento;
    public ?int $id_usuario;
    public string $dt_checkin;
    public string $dt_checkout;
    public string $situation;
    public float $amount;
    public string $type;
    public ?int $guest;
    public ?int $is_deleted;
    public ?string $obs;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): Reserva
    {
        $reserva = new Reserva();
        $reserva->id = $data['id'] ?? null;
        $reserva->uuid = $data['uuid'] ?? $this->generateUUID();
        $reserva->id_apartamento = $data['id_apartamento'];
        $reserva->id_usuario = $data['id_usuario'] ?? null;
        $reserva->dt_checkin = $data['dt_checkin'];
        $reserva->dt_checkout = $data['dt_checkout'];
        $reserva->situation = $data['situation'] ?? 'Reservada';
        $reserva->amount = (float)($data['amount'] ?? 0.0);
        $reserva->type = $data['type'];
        $reserva->is_deleted = $data['is_deleted'] ?? 0;
        $reserva->obs = $data['obs'] ?? null;
        return $reserva;
    }

    public function update(array $data, Reserva $reserva): Reserva
    {
        $reserva->id_apartamento = $data['id_apartamento'] ?? $reserva->id_apartamento;
        $reserva->id_usuario = $data['id_usuario'] ?? $reserva->id_usuario;
        $reserva->dt_checkin = $data['dt_checkin'] ?? $reserva->dt_checkin;
        $reserva->dt_checkout = $data['dt_checkout'] ?? $reserva->dt_checkout;
        $reserva->situation = $data['situation'] ?? $reserva->situation;
        $reserva->amount = isset($data['amount']) ? (float)$data['amount'] : $reserva->amount;
        $reserva->type = $data['type'] ?? $reserva->type;
        $reserva->obs = $data['obs'] ?? $reserva->obs;
        return $reserva;
    }
}
