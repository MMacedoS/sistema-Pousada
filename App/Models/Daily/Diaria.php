<?php

namespace App\Models\Daily;

use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Models\Traits\UuidTrait;

class Diaria
{
    use UuidTrait, UserToPerson;

    public ?int $id;
    public string $uuid;
    public string $dt_daily;
    public float $amount;
    public string $status;
    public ?int $id_usuario;
    public int $id_reserva;
    public int $is_deleted = 0;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): Diaria
    {
        $this->uuid = $data['uuid'] ?? $this->generateUUID();
        $this->id_reserva = $data['id_reserva'];
        $this->dt_daily = $data['dt_daily'];
        $this->amount = $data['amount'];
        $this->status = $data['status'] ?? 'Disponível';
        $this->is_deleted = $data['is_deleted'] ?? $this->is_deleted;
        $this->id_usuario = $data['id_usuario'];
        return $this;
    }

    public function update(array $data, Diaria $diaria): Diaria
    {
        $diaria->id_reserva = $data['id_reserva'] ?? $diaria->id_reserva;
        $diaria->dt_daily = $data['dt_daily'] ?? $diaria->dt_daily;
        $diaria->amount = $data['amount'] ?? $diaria->amount;
        $diaria->status = $data['status'] ?? $diaria->status;
        $diaria->id_usuario = $data['id_usuario'] ?? $diaria->id_usuario;
        $diaria->is_deleted = $data['is_deleted'] ?? $diaria->is_deleted;
        return $diaria;
    }
}
