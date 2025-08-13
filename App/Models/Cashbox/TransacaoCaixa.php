<?php

namespace App\Models\Cashbox;

use App\Models\Traits\UuidTrait;

class TransacaoCaixa
{
    use UuidTrait;

    public ?int $id;
    public string $uuid;
    public int $caixa_id;
    public string $type; // entrada ou saida
    public string $origin;
    public ?string $reference_uuid;
    public ?string $description;
    public string $payment_form; // Dinheiro, PIX, Cartão Crédito, etc
    public int $canceled; // 0 ou 1
    public float $amount;
    public ?int $id_usuario;
    public ?string $created_at;

    public function __construct() {}

    public function create(array $data): TransacaoCaixa
    {
        $transacao = new TransacaoCaixa();
        $transacao->id = $data['id'] ?? null;
        $transacao->uuid = $data['uuid'] ?? $this->generateUUID();
        $transacao->caixa_id = $data['caixa_id'];
        $transacao->type = $data['type']; // entrada ou saida
        $transacao->origin = $data['origin'];
        $transacao->reference_uuid = $data['reference_uuid'] ?? null;
        $transacao->description = $data['description'] ?? null;
        $transacao->payment_form = $data['payment_form'] ?? 'Dinheiro';
        $transacao->canceled = $data['canceled'] ?? 0;
        $transacao->amount = $data['amount'];
        $transacao->id_usuario = $data['id_usuario'] ?? null;
        $transacao->created_at = $data['created_at'] ?? null;

        return $transacao;
    }

    public function update(array $data, TransacaoCaixa $transacao): TransacaoCaixa
    {
        $transacao->type = $data['type'] ?? $transacao->type;
        $transacao->origin = $data['origin'] ?? $transacao->origin;
        $transacao->reference_uuid = $data['reference_uuid'] ?? $transacao->reference_uuid;
        $transacao->description = $data['description'] ?? $transacao->description;
        $transacao->payment_form = $data['payment_form'] ?? $transacao->payment_form;
        $transacao->canceled = $data['canceled'] ?? $transacao->canceled;
        $transacao->amount = $data['amount'] ?? $transacao->amount;

        return $transacao;
    }
}
