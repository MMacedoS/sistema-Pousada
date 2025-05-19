<?php

namespace App\Repositories\Contracts\Person;

use App\Models\Person\PessoaFisica;

interface IPessoaFisicaRepository {

    public function allPersons();

    public function personByUserId(int $user_id);

    public function create(array $data);

    public function update(array $data, int $id);

    public function findPessoaFisica(array $criteria): ?PessoaFisica;

    public function delete(int $id);

    public function remove($id) :?bool; 

    public function findByUuid(string $uuid);

    public function findById(int $id);
}