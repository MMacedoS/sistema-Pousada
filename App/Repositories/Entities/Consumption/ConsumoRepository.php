<?php

namespace App\Repositories\Entities\Consumption;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Consumption\Consumo;
use App\Repositories\Contracts\Consumption\IConsumoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ConsumoRepository extends SingletonInstance implements IConsumoRepository
{
    private const TABLE = 'consumos';
    private const CLASS_NAME = Consumo::class;

    use FindTrait;

    public function __construct()
    {
        $this->model = new Consumo();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $consumo = $this->model->create($data);

        $sql = "INSERT INTO " . self::TABLE . " 
            (uuid, id_reserva, id_produto, amount, quantity, dt_consumption, id_usuario) 
            VALUES (:uuid, :id_reserva, :id_produto, :amount, :quantity, :dt_consumption, :id_usuario)";

        $smtpt = $this->conn->prepare($sql);
        $created = $smtpt->execute([
            'uuid' => $consumo->uuid,
            'id_reserva' => $consumo->id_reserva,
            'id_produto' => $consumo->id_produto,
            'amount' => $consumo->amount,
            'quantity' => $consumo->quantity,
            'dt_consumption' => $consumo->dt_consumption,
            'id_usuario' => $consumo->id_usuario
        ]);

        if ($created) {
            $consumo->id = (int)$this->conn->lastInsertId();
            return $consumo;
        }

        return null;
    }

    public function update(array $data, int $id)
    {
        if (empty($data) || empty($id)) {
            return null;
        }

        $consumption_existing = $this->findById($id);

        if (is_null($consumption_existing)) {
            return null;
        }

        $sql = "UPDATE " . self::TABLE . " SET 
            id_reserva = :id_reserva, 
            id_produto = :id_produto, 
            amount = :amount, 
            quantity = :quantity, 
            dt_consumption = :dt_consumption, 
            id_usuario = :id_usuario 
            WHERE id = :id";

        $smtpt = $this->conn->prepare($sql);
        $updated = $smtpt->execute([
            'id_reserva' => $data['id_reserva'] ?? $consumption_existing->id_reserva,
            'id_produto' => $data['id_produto'] ?? $consumption_existing->id_produto,
            'amount' => $data['amount'] ?? $consumption_existing->amount,
            'quantity' => $data['quantity'] ?? $consumption_existing->quantity,
            'dt_consumption' => $data['dt_consumption'] ?? $consumption_existing->dt_consumption,
            'id_usuario' => $data['id_usuario'] ?? $consumption_existing->id_usuario,
            'id' => $id
        ]);

        if ($updated) {
            return $this->findById($id);
        }

        return null;
    }

    public function delete(int $id)
    {
        if (empty($id)) {
            return false;
        }

        $consumption_existing = $this->findById($id);

        if (is_null($consumption_existing)) {
            return false;
        }

        $sql = "UPDATE " . self::TABLE . " SET is_deleted = 1, status = 0 WHERE id = :id";
        $smtpt = $this->conn->prepare($sql);
        $deleted = $smtpt->execute(['id' => $id]);

        return $deleted && $smtpt->rowCount() > 0;
    }

    public function all(array $params = [])
    {
        if (empty($params)) {
            return [];
        }

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

        $sql .= " ORDER BY dt_consumption DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function totalConsumptionsByReservaId(int $id_reserva): float
    {
        if (empty($id_reserva)) {
            return 0.00;
        }

        $sql = "SELECT SUM(amount * quantity) as total FROM " . self::TABLE . " 
                WHERE id_reserva = :id_reserva AND is_deleted = 0 AND status = 1";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id_reserva' => $id_reserva]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result['total'] ? (float)$result['total'] : 0.00;
        } catch (\PDOException $e) {
            LoggerHelper::logError("Error calculating total consumptions: " . $e->getMessage());
            return 0.00;
        }
    }

    public function findByPeriod(string $startDate, string $endDate)
    {
        $sql = "SELECT c.* 
                FROM " . self::TABLE . " c
                WHERE DATE(c.created_at) BETWEEN :start_date AND :end_date
                ORDER BY c.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {
            LoggerHelper::logError("Error finding consumptions by period: " . $e->getMessage());
            return [];
        }
    }
}
