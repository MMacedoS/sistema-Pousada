<?php

namespace App\Repositories\Contracts\Consumption;

interface IConsumoRepository
{
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function all(array $params = []);
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function findByPeriod(string $startDate, string $endDate);
}
