<?php

namespace App\Repositories\Entities\Daily;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Daily\Diaria;
use App\Models\Reservation\Reserva;
use App\Repositories\Contracts\Daily\IDiariaRepository;
use App\Repositories\Traits\FindTrait;

class DiariaRepository extends SingletonInstance implements IDiariaRepository
{
    private const CLASS_NAME = Diaria::class;
    private const TABLE = "diarias";

    use FindTrait;

    public function __construct()
    {
        $this->model = new Diaria();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findByReservaIdAndDate(int $reservaId, string $date)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE id_reserva = :id_reserva AND dt_daily = :dt_daily AND is_deleted = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_reserva' => $reservaId, ':dt_daily' => $date]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            $diaria = new Diaria();
            foreach ($data as $key => $value) {
                if (property_exists($diaria, $key)) {
                    $diaria->$key = $value;
                }
            }
            return $diaria;
        }
        return null;
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE is_deleted = :is_deleted";
        $conditions = [];
        $bindings = [':is_deleted' => $params['is_deleted'] ?? 0];

        if (isset($params['id_reserva'])) {
            $conditions[] = 'id_reserva = :id_reserva';
            $bindings[':id_reserva'] = $params['id_reserva'];
        }

        if (isset($params['dt_daily'])) {
            $conditions[] = 'dt_daily = :dt_daily';
            $bindings[':dt_daily'] = $params['dt_daily'];
        }

        if (isset($params['status'])) {
            $conditions[] = 'status = :status';
            $bindings[':status'] = $params['status'];
        }

        if (count($conditions) > 0) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY dt_daily DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $existingDiaria = $this->existingDiaria($data);

        if (!is_null($existingDiaria)) {
            return $existingDiaria;
        }

        try {
            $diaria = $this->model->create($data);

            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " (uuid, id_reserva, dt_daily, amount) VALUES (:uuid, :id_reserva, :dt_daily, :amount)"
            );
            $stmt->execute([
                ':uuid' => $diaria->uuid,
                ':id_reserva' => $diaria->id_reserva,
                ':dt_daily' => $diaria->dt_daily,
                ':amount' => $diaria->amount,
            ]);

            $diaria->id = (int)$this->conn->lastInsertId();
            if ($diaria->id) {
                return $diaria;
            }
            return null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public function existingDiaria(array $data)
    {
        if (!isset($data['id_reserva']) || !isset($data['dt_daily'])) {
            return null;
        }

        return $this->findByReservaIdAndDate($data['id_reserva'], $data['dt_daily']);
    }

    public function update(array $data, int $id)
    {
        if (empty($data) || $id <= 0) {
            return null;
        }

        $existingDiaria = $this->findById($id);
        if (is_null($existingDiaria)) {
            return null;
        }

        try {
            $diaria = $this->model->update($data, $existingDiaria);

            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " SET id_reserva = :id_reserva, dt_daily = :dt_daily, amount = :amount, status = :status, is_deleted = :is_deleted WHERE id = :id"
            );
            $stmt->execute([
                ':id_reserva' => $diaria->id_reserva,
                ':dt_daily' => $diaria->dt_daily,
                ':amount' => $diaria->amount,
                ':status' => $diaria->status,
                ':is_deleted' => $diaria->is_deleted,
                ':id' => $id
            ]);

            if ($stmt->rowCount() > 0) {
                return $diaria;
            }
            return null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $existingDiaria = $this->findById($id);
        if (is_null($existingDiaria)) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " SET is_deleted = 1 WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function totalAmountByReservaId(int $reservaId): float
    {
        if ($reservaId <= 0) {
            return 0.0;
        }

        try {
            $stmt = $this->conn->prepare(
                "SELECT SUM(amount) as total_amount FROM " . self::TABLE . " WHERE id_reserva = :id_reserva AND is_deleted = 0"
            );
            $stmt->execute([':id_reserva' => $reservaId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result['total_amount'] !== null ? (float)$result['total_amount'] : 0.0;
        } catch (\Throwable $th) {
            //throw $th;
            return 0.0;
        }
    }

    public function createAllByBetweenDate(
        ?Reserva $reserva,
        string $startDate,
        string $endDate
    ): array {
        if (is_null($reserva) || empty($startDate) || empty($endDate)) {
            return [];
        }

        $createdDiarias = [];
        $currentDate = new \DateTime($startDate, new \DateTimeZone('UTC'));
        $endDate = new \DateTime($endDate, new \DateTimeZone('UTC'));

        while ($currentDate < $endDate) {
            $item = [
                'id_reserva' => $reserva->id,
                'dt_daily' => $currentDate->format('Y-m-d'),
                'amount' => $reserva->amount,
                'status' => 'Disponível',
                'id_usuario' => $reserva->id_usuario,
                'is_deleted' => 0
            ];
            $diaria = $this->create($item);
            if ($diaria) {
                $createdDiarias[] = $diaria;
            }
            $currentDate->modify('+1 day');
        }

        return $createdDiarias;
    }
}
