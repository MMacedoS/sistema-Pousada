<?php

namespace App\Repositories\Contracts\Cashbox;

use App\Models\Financeiro\Caixa;

interface ICaixaRepository
{
    public function all(array $params = []);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function closedCashbox(int $id, array $data);
    public function openedCashbox(int $id_usuario);
}
