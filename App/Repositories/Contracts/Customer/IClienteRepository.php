<?php

namespace App\Repositories\Contracts\Customer;

use App\Models\Customer\Cliente;

interface IClienteRepository
{
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function all();
    public function savePersonAndCustomer(array $data);
    public function create(array $cliente);
    public function updatePersonCustomer(array $data, int $id);
    public function update(array $cliente, int $id);
    public function delete(string $id);
}
