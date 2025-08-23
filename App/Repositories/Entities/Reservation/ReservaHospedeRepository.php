<?php

namespace App\Repositories\Entities\Reservation;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Reservation\ReservaHospedes;
use App\Repositories\Contracts\Reservation\IReservaHospedeRepository;
use App\Repositories\Entities\Customer\ClienteRepository;
use App\Repositories\Traits\FindTrait;

class ReservaHospedeRepository extends SingletonInstance implements IReservaHospedeRepository
{
    private const CLASS_NAME = ReservaHospedes::class;
    private const TABLE = "reserva_hospedes";
    use FindTrait;

    public function __construct()
    {
        $this->model = new ReservaHospedes();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findByReservaId(int $id_reserva)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE id_reserva = :id_reserva";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_reserva', $id_reserva, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();
        return $register ?: null;
    }

    public function findByHospedeId(int $id_hospede)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE id_hospede = :id_hospede";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_hospede', $id_hospede, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();
        return $register ?: null;
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['id_reserva'])) {
            $conditions[] = 'id_reserva = :id_reserva';
            $bindings[':id_reserva'] = $params['id_reserva'];
        }

        if (isset($params['id_hospede'])) {
            $conditions[] = 'id_hospede = :id_hospede';
            $bindings[':id_hospede'] = $params['id_hospede'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data)
    {
        if (empty($data)) return null;

        $reservaHospede = $this->model->create($data);

        $stmt = $this->conn->prepare(
            "INSERT INTO " . self::TABLE . " SET 
                id_reserva = :id_reserva,
                id_hospede = :id_hospede,
                is_primary = :is_primary"
        );

        $stmt->bindParam(':id_reserva', $reservaHospede->id_reserva, \PDO::PARAM_INT);
        $stmt->bindParam(':id_hospede', $reservaHospede->id_hospede, \PDO::PARAM_INT);
        $stmt->bindParam(':is_primary', $reservaHospede->is_primary, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $reservaHospede;
        }

        return null;
    }

    public function update(array $data, int $id_reserva, int $id_hospede)
    {
        if (empty($data)) return null;

        $stmt = $this->conn->prepare(
            "UPDATE " . self::TABLE . " SET 
                id_reserva = :id_reserva,
                id_hospede = :id_hospede,
                is_primary = :is_primary
            WHERE id_reserva = :id_reserva AND id_hospede = :id_hospede"
        );

        $stmt->bindParam(':id_reserva', $id_reserva, \PDO::PARAM_INT);
        $stmt->bindParam(':id_hospede', $id_hospede, \PDO::PARAM_INT);
        $stmt->bindParam(':is_primary', $data['is_primary'], \PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->model->find($id_reserva, $id_hospede);
        }

        return null;
    }

    public function delete(int $id_reserva, int $id_hospede)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM " . self::TABLE . " WHERE id_reserva = :id_reserva AND id_hospede = :id_hospede"
        );

        $stmt->bindParam(':id_reserva', $id_reserva, \PDO::PARAM_INT);
        $stmt->bindParam(':id_hospede', $id_hospede, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
