<?php

namespace App\Repositories\Contracts\Permission;

interface IPermissaoRepository {

    public function all(array $params = []);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete($id);

    public function allByUser(int $id);

    public function findByUuid(string $uuid);

    public function findById(int $id);
}