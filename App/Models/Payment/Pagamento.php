<?php

namespace App\Models\Payment;

use App\Models\Traits\UuidTrait;

class Pagamento
{
    use UuidTrait;

    public $id;
    public ?string $uuid;
    public ?int $id_reserva;
    public ?string $type_payment;
    public ?float $payment_amount;
    public ?string $dt_payment;
    public ?int $id_venda;
    public ?int $status;
    public ?int $id_usuario;
    public ?int $id_caixa;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): Pagamento
    {
        $pagamento = new Pagamento();
        $pagamento->id = $data['id'] ?? null;
        $pagamento->uuid = $data['uuid'] ?? $this->generateUUID();
        $pagamento->id_reserva = $data['id_reserva'] ?? null;
        $pagamento->type_payment = $data['method'] ?? 'cash';
        $pagamento->payment_amount = $data['amount'] ?? 0.00;
        $pagamento->dt_payment = $data['dt_payment'] ?? date('Y-m-d');
        $pagamento->id_venda = $data['id_venda'] ?? null;
        $pagamento->status = $data['status'] ?? 1;
        $pagamento->id_usuario = $data['id_usuario'] ?? null;
        $pagamento->id_caixa = $data['id_caixa'] ?? null;
        $pagamento->created_at = $data['created_at'] ?? null;
        $pagamento->updated_at = $data['updated_at'] ?? null;

        return $pagamento;
    }

    public function update(array $data, Pagamento $pagamento): Pagamento
    {
        $pagamento->id_reserva = $data['id_reserva'] ?? $pagamento->id_reserva;
        $pagamento->type_payment = $data['type_payment'] ?? $pagamento->type_payment;
        $pagamento->payment_amount = $data['payment_amount'] ?? $pagamento->payment_amount;
        $pagamento->dt_payment = $data['dt_payment'] ?? $pagamento->dt_payment;
        $pagamento->id_venda = $data['id_venda'] ?? $pagamento->id_venda;
        $pagamento->status = $data['status'] ?? $pagamento->status;
        $pagamento->id_usuario = $data['id_usuario'] ?? $pagamento->id_usuario;
        $pagamento->id_caixa = $data['id_caixa'] ?? $pagamento->id_caixa;

        return $pagamento;
    }
}
