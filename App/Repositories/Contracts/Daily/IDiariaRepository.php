<?php

namespace App\Repositories\Contracts\Daily;

interface IDiariaRepository
{
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findByReservaIdAndDate(int $reservaId, string $date);
    public function all(array $params = []);
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function totalAmountByReservaId(int $reservaId): float;
    public function findByPeriod(string $startDate, string $endDate);
}
