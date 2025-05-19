<?php 

namespace App\Repositories\Contracts\Person;

interface IPessoaContatoRepository {

    public function allPersons(array $params = []);

    public function saveAll(array $data);

    public function create(array $data);

    public function updateAll(array $data);

    public function update(array $data, int $id);

    public function deleteAll($person_contact_id);

    public function removeAll($id) :?bool;

    public function remove($id) :?bool;

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}