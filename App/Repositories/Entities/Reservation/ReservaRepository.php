<?php

namespace App\Repositories\Entities\Reservation;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Reservation\Reserva;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Repositories\Entities\Apartments\ApartamentoRepository;
use App\Repositories\Entities\Daily\DiariaRepository;
use App\Repositories\Entities\Reservation\ReservaHospedeRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ReservaRepository extends SingletonInstance implements IReservaRepository
{
    private const CLASS_NAME = Reserva::class;
    private const SITUATION_CHECKED_OUT = 'Finalizada';
    private const SITUATION_AVAILABLE = 'Disponível';
    private const SITUATION_OCCUPIED = 'Ocupado';
    private const SITUATION_CANCELED = 'Cancelada';
    private const SITUATION_BOOKED = 'Reservada';
    private const SITUATION_RESERVED = 'Reservada';
    private const SITUATION_CONFIRMED = 'Confirmada';
    private const SITUATION_HOSTED = 'Hospedada';
    private const TABLE = "reservas";
    private $reservaHospedeRepository;
    private $diariaRepository;
    private $apartamentoRepository;

    use FindTrait;

    public function __construct()
    {
        $this->model = new Reserva();
        $this->conn = Database::getInstance()->getConnection();
        $this->reservaHospedeRepository = ReservaHospedeRepository::getInstance();
        $this->diariaRepository = DiariaRepository::getInstance();
        $this->apartamentoRepository = ApartamentoRepository::getInstance();
    }

    public function all(array $params = [])
    {
        $sql = "
        SELECT r.* FROM reservas r 
        inner join apartamentos a on r.id_apartamento = a.id
        inner join usuarios u on r.id_usuario = u.id 
        inner join reserva_hospedes rh on r.id = rh.id_reserva 
        inner join clientes c on rh.id_hospede = c.id 
        inner join pessoa_fisica pf on c.pessoa_fisica_id = pf.id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['situation'])) {
            $conditions[] = 'r.situation = :situation';
            $bindings[':situation'] = $params['situation'];
        }

        if (isset($params['type'])) {
            $conditions[] = 'r.type = :type';
            $bindings[':type'] = $params['type'];
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $conditions[] = 'r.dt_checkin >= :start_date AND r.dt_checkout <= :end_date';
            $bindings[':start_date'] = $params['start_date'];
            $bindings[':end_date'] = $params['end_date'];
        }

        if (isset($params['search'])) {
            $conditions[] = '(pf.name LIKE :search OR a.name LIKE :search OR u.name LIKE :search OR r.dt_checkin LIKE :search OR r.dt_checkout LIKE :search OR r.situation LIKE :search OR r.amount LIKE :search OR r.type LIKE :search OR r.obs LIKE :search)';
            $bindings[':search'] = '%' . $params['search'] . '%';
        }

        $conditions[] = 'r.is_deleted = :is_deleted';
        $bindings[':is_deleted'] = $params['is_deleted'] ?? 0;

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

        $reserva = $this->model->create($data);
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO reservas SET 
                    uuid = :uuid,
                    id_apartamento = :id_apartamento,
                    id_usuario = :id_usuario,
                    dt_checkin = :dt_checkin,
                    dt_checkout = :dt_checkout,
                    situation = :situation,
                    amount = :amount,
                    type = :type,
                    obs = :obs"
            );
            $ok = $stmt->execute([
                ':uuid' => $reserva->uuid,
                ':id_apartamento' => $reserva->id_apartamento,
                ':id_usuario' => $reserva->id_usuario,
                ':dt_checkin' => $reserva->dt_checkin,
                ':dt_checkout' => $reserva->dt_checkout,
                ':situation' => $reserva->situation,
                ':amount' => $reserva->amount,
                ':type' => $reserva->type,
                ':obs' => $reserva->obs,
            ]);

            if (!$ok) {
                $this->conn->rollBack();
                return null;
            }

            if (isset($data['customer_id']) && !empty($data['customer_id'])) {
                $reserva = $this->findByUuid($reserva->uuid);
                $hospedeData = [
                    'id_reserva' => $reserva->id,
                    'id_hospede' => $data['customer_id'],
                    'is_primary' => 1,
                ];
                $reservaHospede = $this->reservaHospedeRepository->create($hospedeData);
                if (is_null($reservaHospede)) {
                    $this->conn->rollBack();
                    return null;
                }
            }

            $this->conn->commit();
            return $this->findByUuid($reserva->uuid);
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;

        $reserva = $this->model->update($data, $reserva);

        $stmt = $this->conn->prepare(
            "UPDATE reservas SET 
                id_apartamento = :id_apartamento,
                id_usuario = :id_usuario,
                dt_checkin = :dt_checkin,
                dt_checkout = :dt_checkout,
                situation = :situation,
                amount = :amount,
                type = :type,
                obs = :obs
             WHERE id = :id"
        );

        $ok = $stmt->execute([
            ':id_apartamento' => $reserva->id_apartamento,
            ':id_usuario' => $reserva->id_usuario,
            ':dt_checkin' => $reserva->dt_checkin,
            ':dt_checkout' => $reserva->dt_checkout,
            ':situation' => $reserva->situation,
            ':amount' => $reserva->amount,
            ':type' => $reserva->type,
            ':obs' => $reserva->obs,
            ':id' => $id,
        ]);

        if (!$ok) return null;
        return $this->findById($id);
    }

    public function delete(int $id)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;

        $stmt = $this->conn->prepare("UPDATE reservas SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]) ? $reserva : null;
    }

    public function availableApartments(array $params = [])
    {
        $sql = "SELECT a.*
                FROM apartamentos a
                LEFT JOIN reservas r 
                ON a.id = r.id_apartamento
                AND (
                    r.dt_checkin < :end_date
                    AND r.dt_checkout > :start_date
                    AND r.situation IN ('Reservada', 'Confirmada', 'Hospedada')
                    AND r.is_deleted = 0
                )
                WHERE r.id_apartamento IS NULL";

        $bindings = [
            ':start_date' => $params['check_in'],
            ':end_date'   => $params['check_out'],
        ];

        $conditions = [];
        if (!empty($params['category'])) {
            $conditions[] = 'a.category = :category';
            $bindings[':category'] = $params['category'];
        }

        if (!empty($params['name'])) {
            $conditions[] = 'a.name = :name';
            $bindings[':name'] = $params['name'];
        }

        $conditions[] = 'a.active = :active';
        $bindings[':active'] = $params['active'] ?? 1;
        if ($conditions) {
            $sql .= ' AND ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    public function isApartmentAvailable(int $apartmentId, string $startDate, string $endDate, ?int $excludeReservationId = null): bool
    {
        $sql = "SELECT 1
                FROM reservas r
                WHERE r.id_apartamento = :apartment_id
                  AND r.is_deleted = 0
                  AND r.situation IN ('Reservada', 'Confirmada', 'Hospedada')
                  AND r.dt_checkin < :end_date
                  AND r.dt_checkout > :start_date";

        $bindings = [
            ':apartment_id' => $apartmentId,
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ];

        if (!is_null($excludeReservationId)) {
            $sql .= ' AND r.id <> :exclude_id';
            $bindings[':exclude_id'] = $excludeReservationId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        $conflict = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $conflict === false;
    }

    public function changeApartment(string $uuid, array $params = [])
    {
        $reserva = $this->findByUuid($uuid);
        if (is_null($reserva)) return null;
        if (empty($params['id_apartamento'])) return null;

        $stmt = $this->conn->prepare("UPDATE reservas SET id_apartamento = :id_apartamento WHERE id = :id");
        $ok = $stmt->execute([':id_apartamento' => $params['id_apartamento'], ':id' => $reserva->id]);
        return $ok ? $this->findById($reserva->id) : null;
    }

    public function financialReport(array $params = [])
    {
        $sql = "SELECT DATE(r.dt_checkin) as ref_date, SUM(r.amount) as total_amount, COUNT(*) as qty
                FROM reservas r
                WHERE r.is_deleted = 0";
        $bindings = [];
        if (!empty($params['start_date'])) {
            $sql .= ' AND r.dt_checkin >= :start_date';
            $bindings[':start_date'] = $params['start_date'];
        }
        if (!empty($params['end_date'])) {
            $sql .= ' AND r.dt_checkout <= :end_date';
            $bindings[':end_date'] = $params['end_date'];
        }
        if (!empty($params['situation'])) {
            $sql .= ' AND r.situation = :situation';
            $bindings[':situation'] = $params['situation'];
        }
        $sql .= ' GROUP BY ref_date ORDER BY ref_date ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function statusReport(array $params = [])
    {
        $sql = "SELECT r.situation, COUNT(*) as qty
                FROM reservas r
                WHERE r.is_deleted = 0";
        $bindings = [];
        if (!empty($params['start_date'])) {
            $sql .= ' AND r.dt_checkin >= :start_date';
            $bindings[':start_date'] = $params['start_date'];
        }
        if (!empty($params['end_date'])) {
            $sql .= ' AND r.dt_checkout <= :end_date';
            $bindings[':end_date'] = $params['end_date'];
        }
        $sql .= ' GROUP BY r.situation ORDER BY qty DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countReport(array $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM reservas r WHERE r.is_deleted = 0";
        $bindings = [];
        if (!empty($params['start_date'])) {
            $sql .= ' AND r.dt_checkin >= :start_date';
            $bindings[':start_date'] = $params['start_date'];
        }
        if (!empty($params['end_date'])) {
            $sql .= ' AND r.dt_checkout <= :end_date';
            $bindings[':end_date'] = $params['end_date'];
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findFirstByApartmentId(int $apartmentId)
    {
        $today = date('Y-m-d 00:00:00');

        $sql = "SELECT *
            FROM reservas
            WHERE 
                id_apartamento = :id_apartamento
                AND is_deleted = 0
                AND (
                    (situation IN (:situation_reserved, :situation_confirmed)
                    AND :today BETWEEN DATE(dt_checkin) AND DATE(dt_checkout))
                    OR situation = :situation_hospedada
                )
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_apartamento' => $apartmentId,
            ':today'          => $today,
            ':situation_reserved' => self::SITUATION_RESERVED,
            ':situation_confirmed' => self::SITUATION_CONFIRMED,
            ':situation_hospedada' => self::SITUATION_HOSTED,
        ]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch() ?: null;
    }

    public function checkIn(string $id, string $userId)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;
        if ($reserva->situation !== self::SITUATION_RESERVED && $reserva->situation !== self::SITUATION_CONFIRMED) return null;

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE reservas SET situation = :situation, id_usuario = :id_usuario WHERE id = :id");
            $ok = $stmt->execute([':situation' => self::SITUATION_HOSTED, ':id_usuario' => $userId, ':id' => $reserva->id]);
            if (!$ok) {
                $this->conn->rollBack();
                return null;
            }

            $apartmentUpdated = $this->apartamentoRepository->updateStatus($reserva->id_apartamento, self::SITUATION_OCCUPIED);
            if (!$apartmentUpdated) {
                $this->conn->rollBack();
                return null;
            }

            $this->diariaRepository->createAllByBetweenDate($reserva, $reserva->dt_checkin, $reserva->dt_checkout);

            $this->conn->commit();
            return $this->findById($reserva->id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Error during check-in process: " . $e->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function checkout(string $id)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;
        if ($reserva->situation !== self::SITUATION_HOSTED) return null;

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE reservas SET situation = :situation WHERE id = :id");
            $ok = $stmt->execute([':situation' => self::SITUATION_CHECKED_OUT, ':id' => $reserva->id]);
            if (!$ok) {
                $this->conn->rollBack();
                return null;
            }

            $apartmentUpdated = $this->apartamentoRepository->updateStatus($reserva->id_apartamento, self::SITUATION_AVAILABLE);
            if (!$apartmentUpdated) {
                $this->conn->rollBack();
                return null;
            }

            $this->diariaRepository->updateStatusByReservaId($reserva->id, self::SITUATION_CHECKED_OUT);

            $this->conn->commit();
            return $this->findById($reserva->id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Error during check-out process: " . $e->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }
}
