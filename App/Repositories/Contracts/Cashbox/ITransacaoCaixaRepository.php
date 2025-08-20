<?php

namespace App\Repositories\Contracts\Cashbox;

use App\Models\Cashbox\TransacaoCaixa;

interface ITransacaoCaixaRepository
{
    public function all(array $params = []);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function create(array $data);
    public function update(array $data, int $id);
    public function byCaixaId(int $id_caixa);
    public function cancelledTransaction(int $id);
    public function updateTransaction(array $data, int $id);
}
