<?php

namespace App\Repositories\Entities\Reservation;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Reservation\Reserva;
use App\Repositories\Contracts\Reservation\IReservaRepository;
use App\Repositories\Entities\Apartments\ApartamentoRepository;
use App\Repositories\Entities\Daily\DiariaRepository;
use App\Repositories\Entities\Reservation\ReservaHospedeRepository;
use App\Repositories\Entities\Payment\PagamentoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use Monolog\Logger;

class ReservaRepository extends SingletonInstance implements IReservaRepository
{
    private const CLASS_NAME = Reserva::class;
    private const SITUATION_CHECKED_OUT = 'Finalizada';
    private const SITUATION_AVAILABLE = 'Disponivel';
    private const SITUATION_OCCUPIED = 'Ocupado';
    private const SITUATION_CANCELED = 'Cancelada';
    private const SITUATION_BOOKED = 'Reservada';
    private const SITUATION_RESERVED = 'Reservada';
    private const SITUATION_CONFIRMED = 'Confirmada';
    private const SITUATION_HOSTED = 'Hospedada';
    private const TABLE = "reservas";
    private const SITUATION_IMPEDED = 'Impedido';
    private const IS_NOT_DELETED = 0;
    private const IS_DELETED = 1;
    private $reservaHospedeRepository;
    private $diariaRepository;
    private $apartamentoRepository;
    private $pagamentoRepository;

    use FindTrait;

    public function __construct()
    {
        $this->model = new Reserva();
        $this->conn = Database::getInstance()->getConnection();
        $this->reservaHospedeRepository = ReservaHospedeRepository::getInstance();
        $this->diariaRepository = DiariaRepository::getInstance();
        $this->apartamentoRepository = ApartamentoRepository::getInstance();
        $this->pagamentoRepository = PagamentoRepository::getInstance();
    }

    private function getCurrentDateWithTimezone(string $format = 'Y-m-d'): string
    {
        $timezone = new \DateTimeZone($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');
        $dateTime = new \DateTime('now', $timezone);
        return $dateTime->format($format);
    }

    private function ensureDateAsString($date): string
    {
        if (is_string($date)) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        return (string) $date;
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
            if ($params['situation'] !== 'all') {
                $conditions[] = 'r.situation = :situation';
                $bindings[':situation'] = $params['situation'];
            }
        }

        if (!isset($params['situation']) || empty($params['situation'])) {
            $conditions[] = 'r.situation IN (:situation_reserved, :situation_confirmed, :situation_hosted)';
            $bindings[':situation_reserved'] = self::SITUATION_RESERVED;
            $bindings[':situation_confirmed'] = self::SITUATION_CONFIRMED;
            $bindings[':situation_hosted'] = self::SITUATION_HOSTED;
        }

        if (isset($params['type'])) {
            $conditions[] = 'r.type = :type';
            $bindings[':type'] = $params['type'];
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $conditions[] = ' (r.dt_checkin >= :start_date  OR  r.dt_checkout <= :end_date)';
            $bindings[':start_date'] = $params['start_date'];
            $bindings[':end_date'] = $params['end_date'];
        }

        if (isset($params['search'])) {
            $conditions[] = '(
                pf.name LIKE :search 
                OR a.name LIKE :search 
                OR u.name LIKE :search 
                OR r.amount LIKE :search 
                OR r.obs LIKE :search)';
            $bindings[':search'] = '%' . $params['search'] . '%';
        }

        $conditions[] = 'r.is_deleted = :is_deleted';
        $bindings[':is_deleted'] = $params['is_deleted'] ?? self::IS_NOT_DELETED;

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
                    guest = :guest,
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
                ':guest' => $reserva->guest,
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
                guest = :guest,
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
            ':guest' => $reserva->guest,
            ':amount' => $reserva->amount,
            ':type' => $reserva->type,
            ':obs' => $reserva->obs,
            ':id' => $id,
        ]);

        if (!$ok) return null;

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $reserva = $this->findById($id);
            $hospedeData = [
                'id_reserva' => $reserva->id,
                'id_hospede' => $data['customer_id'],
                'is_primary' => 1,
            ];
            $reservaHospede = $this->reservaHospedeRepository->update($hospedeData);
            if (is_null($reservaHospede)) return null;
        }

        if ($reserva->situation === self::SITUATION_CANCELED) {
            $this->diariaRepository->updateStatusByReservaId($reserva->id, self::SITUATION_CANCELED);
        }

        if ($reserva->situation === self::SITUATION_CHECKED_OUT) {
            $this->diariaRepository->updateStatusByReservaId($reserva->id, self::SITUATION_CHECKED_OUT);
        }

        if ($reserva->situation === self::SITUATION_HOSTED) {
            $checkin = $this->ensureDateAsString($reserva->dt_checkin);
            $checkout = $this->ensureDateAsString($reserva->dt_checkout);

            $this->diariaRepository->createAllByBetweenDate($reserva, $checkin, $checkout);
        }

        return $this->findById($id);
    }

    public function delete(int $id)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE reservas SET is_deleted = :is_deleted, situation = :situation WHERE id = :id");
            $ok = $stmt->execute([':id' => $id, ':is_deleted' => self::IS_DELETED, ':situation' => self::SITUATION_CANCELED]);
            if (!$ok) {
                $this->conn->rollBack();
                return null;
            }

            $this->diariaRepository->updateStatusByReservaId($reserva->id, self::SITUATION_CANCELED);

            $pagamentos = $this->pagamentoRepository->findByReserva($reserva->id);
            foreach ($pagamentos as $pagamento) {
                try {
                    $this->pagamentoRepository->cancelPayment($pagamento->id);
                } catch (\Throwable $th) {
                    LoggerHelper::logError('Falha ao cancelar pagamento ID ' . $pagamento->id . ' da reserva ' . $reserva->id . ': ' . $th->getMessage());
                }
            }

            $this->conn->commit();

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError('Erro ao cancelar/deletar reserva ' . $id . ': ' . $e->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function availableApartments(array $params = [])
    {
        $sql = "SELECT a.*
        FROM apartamentos a
        LEFT JOIN reservas r 
            ON a.id = r.id_apartamento
            AND (
                DATE(r.dt_checkin) <= DATE(:end_date)
                AND DATE(r.dt_checkout) > DATE(:start_date)
                AND (
                    (r.situation IN (:reserved, :confirmed) AND r.is_deleted = :is_deleted)
                    OR
                    (r.situation = :hosted AND r.is_deleted = :is_deleted AND DATE(r.dt_checkout) > DATE(:start_date))
                )
            )
        WHERE r.id_apartamento IS NULL AND a.situation != :situation_none";

        $bindings = [
            ':start_date' => $params['check_in'],
            ':end_date'   => $params['check_out'],
            ':reserved'   => self::SITUATION_RESERVED,
            ':confirmed'  => self::SITUATION_CONFIRMED,
            ':hosted'     => self::SITUATION_HOSTED,
            ':is_deleted' => self::IS_NOT_DELETED,
            ':situation_none' => self::SITUATION_IMPEDED,
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
        $today = $this->getCurrentDateWithTimezone('Y-m-d 00:00:00');

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

        if (!$this->isValidForCheckIn($reserva)) {
            return null;
        }

        try {
            $this->conn->beginTransaction();

            $updatedReservation = $this->updateReservationToHosted($reserva, $userId);
            if (!$updatedReservation) {
                $this->conn->rollBack();
                return null;
            }

            $apartmentUpdated = $this->updateApartmentStatusToOccupied($reserva->id_apartamento);
            if (!$apartmentUpdated) {
                $this->conn->rollBack();
                return null;
            }

            $this->createDiariasIfNeeded($reserva);

            $this->conn->commit();

            return $this->findById($reserva->id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro durante o processo de check-in para a reserva {$reserva->id}: " . $e->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function processAutoCheckIn(int $reservaId, string $userId)
    {
        $reserva = $this->findById($reservaId);

        if (is_null($reserva)) {
            return false;
        }

        if ($this->diariaRepository->hasExistingDiariasForReservation($reservaId)) {
            LoggerHelper::logInfo("Check-in automático ignorado: diárias já existem para reserva {$reservaId}");
            return true;
        }

        try {
            $apartmentUpdated = $this->updateApartmentStatusToOccupied($reserva->id_apartamento);
            if (!$apartmentUpdated) {
                return false;
            }

            $this->createDiariasIfNeeded($reserva);

            return true;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao processar check-in automático para reserva {$reservaId}: " . $e->getMessage());
            return false;
        }
    }

    private function isValidForCheckIn(?Reserva $reserva): bool
    {
        if (is_null($reserva)) {
            LoggerHelper::logError("Reserva não encontrada para check-in");
            return false;
        }

        $validSituations = [self::SITUATION_RESERVED, self::SITUATION_CONFIRMED];
        if (!in_array($reserva->situation, $validSituations)) {
            LoggerHelper::logError("Situação de reserva inválida para check-in: {$reserva->situation}");
            return false;
        }

        // Verifica se a data de check-in não é anterior ao dia atual
        $today = $this->getCurrentDateWithTimezone('Y-m-d');
        $checkinDate = $this->ensureDateAsString($reserva->dt_checkin);

        if ($checkinDate < $today) {
            LoggerHelper::logError("Data de check-in está no passado: {$checkinDate} < {$today}");
            return false;
        }

        return true;
    }

    private function updateReservationToHosted(Reserva $reserva, string $userId): bool
    {
        $stmt = $this->conn->prepare("UPDATE reservas SET situation = :situation, id_usuario = :id_usuario WHERE id = :id");
        return $stmt->execute([
            ':situation' => self::SITUATION_HOSTED,
            ':id_usuario' => $userId,
            ':id' => $reserva->id
        ]);
    }

    private function updateApartmentStatusToOccupied(int $apartmentId): bool
    {
        $updatedApartment = $this->apartamentoRepository->updateStatus($apartmentId, self::SITUATION_OCCUPIED);
        return !is_null($updatedApartment);
    }

    private function createDiariasIfNeeded(Reserva $reserva): void
    {
        $checkin = $this->ensureDateAsString($reserva->dt_checkin);
        $checkout = $this->ensureDateAsString($reserva->dt_checkout);

        $this->diariaRepository->createAllByBetweenDate($reserva, $checkin, $checkout);
    }

    public function checkout(string $id, string $userId)
    {
        $reserva = $this->findById($id);
        if (is_null($reserva)) return null;
        if ($reserva->situation !== self::SITUATION_HOSTED) return null;

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE reservas SET situation = :situation, id_usuario = :id_usuario WHERE id = :id");
            $ok = $stmt->execute([':situation' => self::SITUATION_CHECKED_OUT, ':id_usuario' => $userId, ':id' => $reserva->id]);
            if (!$ok) {
                $this->conn->rollBack();
                return null;
            }

            $apartmentUpdated = $this->apartamentoRepository->updateStatus($reserva->id_apartamento, self::SITUATION_AVAILABLE);
            if (is_null($apartmentUpdated)) {
                $this->conn->rollBack();
                return null;
            }

            $this->diariaRepository->updateStatusByReservaId($reserva->id, self::SITUATION_CHECKED_OUT);

            $this->conn->commit();
            return $this->findById($reserva->id);
        } catch (\Exception $e) {
            LoggerHelper::logError("erro durante o processo de check-out: " . $e->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function getCheckinToday(string $date): array
    {
        $sql = "SELECT *
                FROM reservas
                WHERE DATE(dt_checkin) = DATE(:dt_checkin)
                AND situation IN (:situation_reserved, :situation_confirmed)
                AND is_deleted = :is_deleted";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':dt_checkin' => $date,
            ':is_deleted' => self::IS_NOT_DELETED,
            ':situation_reserved' => self::SITUATION_RESERVED,
            ':situation_confirmed' => self::SITUATION_CONFIRMED
        ]);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function getCheckoutTodayOrLate(string $date): array
    {
        $sql = "SELECT *
                FROM reservas
                WHERE DATE(dt_checkout) <= DATE(:dt_checkout)
                AND situation = :situation
                AND is_deleted = :is_deleted";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':dt_checkout' => $date,
            ':situation' => self::SITUATION_HOSTED,
            ':is_deleted' => self::IS_NOT_DELETED
        ]);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function getCurrentGuestsCount(): int
    {
        $sql = "SELECT sum(guest) as guest_count
                FROM reservas
                WHERE situation = :situation
                AND is_deleted = :is_deleted";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':situation' => self::SITUATION_HOSTED,
            ':is_deleted' => self::IS_NOT_DELETED
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['guest_count'] : 0;
    }

    public function findExistingReservation(int $apartmentId, int $customerId, string $checkIn, string $checkOut): ?object
    {
        $sql = "SELECT r.*
                FROM reservas r
                INNER JOIN reserva_hospedes rh ON r.id = rh.id_reserva
                WHERE r.id_apartamento = :apartment_id
                  AND rh.id_hospede = :customer_id
                  AND r.dt_checkin = :check_in
                  AND r.dt_checkout = :check_out
                  AND r.is_deleted = 0
                  AND r.situation IN ('Reservada', 'Confirmada', 'Hospedada')
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':apartment_id' => $apartmentId,
            ':customer_id' => $customerId,
            ':check_in' => $checkIn,
            ':check_out' => $checkOut
        ]);

        $result = $stmt->fetchObject(self::CLASS_NAME);
        return $result !== false ? $result : null;
    }
}
