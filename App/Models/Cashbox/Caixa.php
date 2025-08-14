<?php

namespace App\Models\Cashbox;

use App\Models\Traits\UuidTrait;

class Caixa
{
    use UuidTrait;

    public ?int $id;
    public string $uuid;
    public int $id_usuario_opened;
    public ?int $id_usuario_closed;
    public string $opened_at;
    public ?string $closed_at;
    public float $initial_amount;
    public float $current_balance;
    public ?float $final_amount;
    public ?float $difference;
    public $transactions;
    public string $status; // aberto, fechado, cancelado
    public ?string $obs;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): Caixa
    {
        $caixa = new Caixa();
        $caixa->id = $data['id'] ?? null;
        $caixa->uuid = $data['uuid'] ?? $this->generateUUID();
        $caixa->id_usuario_opened = $data['id_usuario_opened'];
        $caixa->id_usuario_closed = $data['id_usuario_closed'] ?? null;
        $caixa->opened_at = $data['opened_at'] ?? date('Y-m-d H:i:s');
        $caixa->closed_at = $data['closed_at'] ?? null;
        $caixa->initial_amount = isset($data['initial_amount']) ? (float) $data['initial_amount'] : 0.00;
        $caixa->current_balance = isset($data['current_balance']) ? (float) $data['current_balance'] : (float) $data['initial_amount'] ?? 0.00;
        $caixa->final_amount = isset($data['final_amount']) ? (float) $data['final_amount'] : null;
        $caixa->difference = isset($data['difference']) ? (float) $data['difference'] : null;
        $caixa->status = $data['status'] ?? 'aberto';
        $caixa->obs = $data['obs'] ?? null;
        $caixa->created_at = $data['created_at'] ?? null;
        $caixa->updated_at = $data['updated_at'] ?? null;

        return $caixa;
    }

    public function update(array $data, Caixa $caixa): Caixa
    {
        $caixa->id_usuario_closed = $data['id_usuario_closed'] ?? $caixa->id_usuario_closed;
        $caixa->closed_at = $data['closed_at'] ?? $caixa->closed_at;
        $caixa->final_amount = isset($data['final_amount']) ? (float) $data['final_amount'] : $caixa->final_amount;
        $caixa->difference = isset($data['difference']) ? (float) $data['difference'] : $caixa->difference;
        $caixa->status = $data['status'] ?? $caixa->status;
        $caixa->obs = $data['obs'] ?? $caixa->obs;

        return $caixa;
    }
}
