<?php

namespace App\Models\Consumption;

use App\Models\Traits\UuidTrait;

class Consumo
{
    use UuidTrait;

    public ?int $id;
    public ?string $uuid;
    public ?int $id_reserva;
    public ?int $id_produto;
    public ?int $id_usuario;
    public ?float $amount;
    public ?int $quantity;
    public ?string $dt_consumption;
    public ?int $status;
    public ?int $is_deleted;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): Consumo
    {
        $consumo = new Consumo();
        $consumo->id = $data['id'] ?? null;
        $consumo->uuid = $data['uuid'] ?? $this->generateUUID();
        $consumo->id_reserva = $data['id_reserva'] ?? null;
        $consumo->id_produto = $data['product_id'] ?? null;
        $consumo->amount = $data['amount'] ?? 0.00;
        $consumo->quantity = $data['quantity'] ?? 1;
        $consumo->dt_consumption = $data['dt_consumption'] ?? date('Y-m-d');
        $consumo->status = $data['status'] ?? 1;
        $consumo->id_usuario = $data['id_usuario'] ?? null;
        $consumo->is_deleted = $data['is_deleted'] ?? 0;
        $consumo->created_at = $data['created_at'] ?? null;
        $consumo->updated_at = $data['updated_at'] ?? null;

        return $consumo;
    }

    public function update(array $data, Consumo $consumo): Consumo
    {
        $consumo->id_reserva = $data['id_reserva'] ?? $consumo->id_reserva;
        $consumo->id_produto = $data['product_id'] ?? $consumo->id_produto;
        $consumo->amount = $data['amount'] ?? $consumo->amount;
        $consumo->dt_consumption = $data['dt_consumption'] ?? $consumo->dt_consumption;
        $consumo->status = $data['status'] ?? $consumo->status;
        $consumo->quantity = $data['quantity'] ?? $consumo->quantity;
        $consumo->is_deleted = $data['is_deleted'] ?? $consumo->is_deleted;

        return $consumo;
    }
}
