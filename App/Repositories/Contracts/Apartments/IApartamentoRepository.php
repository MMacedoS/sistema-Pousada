<?php

namespace App\Repositories\Contracts\Apartments;

interface IApartamentoRepository {

    public function all(array $params = []);

    public function apartmentsAvailable(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function changeActiveStatus(int $id);

    public function findByUuid(string $uuid);

    public function findById(int $id);
}