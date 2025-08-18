<?php

namespace App\Repositories\Contracts\Sale;

interface IVendaRepository
{
    public function all(array $params = []);

    public function create(array $data);

    public function findById(int $id);

    public function findByUuid(string $uuid);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByMesa(int $mesaId);

    public function findByUsuario(int $usuarioId);

    public function findByPeriod(string $startDate, string $endDate);

    public function getTotalSales(array $filters = []);

    public function closeSale(int $id);

    public function cancelSale(int $id);
}
