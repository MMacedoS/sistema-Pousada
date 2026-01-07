<?php

namespace App\Models\Sale;

use App\Models\Traits\UuidTrait;

class Venda
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?string $dt_sale;
    public ?string $name;
    public ?string $description;
    public ?float $amount_sale;
    public ?string $status;
    public ?int $id_reserva;
    public ?int $id_usuario;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): Venda
    {
        $venda = new Venda();
        $venda->id = $data['id'] ?? null;
        $venda->uuid = $data['uuid'] ?? $this->generateUUID();
        $venda->dt_sale = $data['dt_sale'] ?? date('Y-m-d');
        $venda->name = $data['sale_name'] ?? null;
        $venda->description = $data['description'] ?? null;
        $venda->amount_sale = $data['amount'] ?? 0.00;
        $venda->status = $data['status'] ?? "Pendente";
        $venda->id_reserva = $data['reservation_id'] ?? null;
        $venda->id_usuario = $data['id_usuario'] ?? null;
        $venda->created_at = $data['created_at'] ?? null;
        $venda->updated_at = $data['updated_at'] ?? null;

        return $venda;
    }

    public function update(array $data, Venda $venda): Venda
    {
        $venda->dt_sale = $data['dt_sale'] ?? $venda->dt_sale;
        $venda->name = $data['sale_name'] ?? $venda->name;
        $venda->description = $data['description'] ?? $venda->description;
        $venda->amount_sale = $data['amount_sale'] ?? $venda->amount_sale;
        $venda->status = $data['status'] ?? $venda->status;
        $venda->id_reserva = $data['id_reserva'] ?? $venda->id_reserva;
        $venda->id_usuario = $data['id_usuario'] ?? $venda->id_usuario;

        return $venda;
    }
}
