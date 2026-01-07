<?php

namespace App\Repositories\Contracts\Reservation;

interface IReservaHospedeRepository
{
    public function findByReservaId(int $id_reserva);
    public function findByHospedeId(int $id_hospede);
    public function all(array $params = []);
    public function create(array $data);
    public function update(array $data);
    public function delete(int $id_reserva, int $id_hospede);
}
