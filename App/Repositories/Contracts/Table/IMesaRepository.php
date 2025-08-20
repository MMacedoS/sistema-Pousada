<?php

namespace App\Repositories\Contracts\Table;

interface IMesaRepository
{
    public function all(array $params = []);

    public function create(array $data);

    public function findById(int $id);

    public function findByUuid(string $uuid);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByStatus(string $status);

    public function occupyTable(int $id, int $vendaId);

    public function freeTable(int $id);

    public function closeTable(int $id);

    public function getAvailableTables();

    public function getOccupiedTables();
}
