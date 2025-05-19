<?php

namespace App\Repositories\Contracts\User;

interface IUsuarioRepository {
    
    public function all(array $params = []);

    public function create(array $data, bool $forceNewPassword = true);

    public function findByEmail(string $email);

    public function findByEmailAndSector(string $email);

    public function update(array $data, int $id);

    public function updatePassword(array $data, int $id);

    public function getLogin(string $email, string $senha);

    public function delete(int $id);

    public function remove($id) :?bool;

    public function findPermissions(int $usuario_id);

    public function addPermissions(array $data, int $id): bool;

    public function removePermissions(int $usuario_id): bool;

    public function updatePhoto($file, $dir, $id_user);

    public function findByUuid(string $uuid);

    public function findById(int $id);
}