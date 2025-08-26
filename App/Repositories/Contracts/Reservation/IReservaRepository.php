<?php

namespace App\Repositories\Contracts\Reservation;

interface IReservaRepository
{
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function all(array $params = []);
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function availableApartments(array $params = []);
    public function isApartmentAvailable(int $apartmentId, string $startDate, string $endDate, ?int $excludeReservationId = null): bool;
    public function changeApartment(string $uuid, array $params = []);
    public function financialReport(array $params = []);
    public function statusReport(array $params = []);
    public function countReport(array $params = []);
    public function checkIn(string $uuid, string $userId);
}
