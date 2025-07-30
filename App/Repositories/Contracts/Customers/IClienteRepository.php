<?php

namespace App\Repositories\Contracts\Customers;

interface IClienteRepository {
    public function all(array $criteria);

    public function create(array $params);

    public function update(array $params, int $id);

    public function delete(int $id);
}