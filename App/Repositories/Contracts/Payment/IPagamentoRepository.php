<?php

namespace App\Repositories\Contracts\Payment;

interface IPagamentoRepository
{
    public function all(array $params = []);

    public function create(array $data);

    public function findById(int $id);

    public function findByUuid(string $uuid);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByVenda(int $vendaId);

    public function findByReserva(int $reservaId);

    public function findByCaixa(int $caixaId);

    public function findByPeriod(string $startDate, string $endDate);

    public function getTotalByType(string $type);

    public function cancelPayment(int $id);

    public function getRevenueByPeriod($startDate, $endDate);
}
