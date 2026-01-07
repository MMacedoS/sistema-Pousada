<?php

namespace App\Repositories\Entities\Cashbox;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Cashbox\Caixa;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Traits\FindTrait;

class CaixaRepository extends SingletonInstance implements ICaixaRepository
{
    private const CLASS_NAME = Caixa::class;
    private const TABLE = "caixas";
    private const FORM_CASH = 'cash';
    private const TYPE_INPUT = 'entrada';
    private const TYPE_OUTPUT = 'saida';
    private const TYPE_BLOOD = 'sangria';
    private const OPEN = "aberto";
    private const CLOSED = "fechado";
    private const DISABLED = 1;

    use FindTrait;

    public function __construct()
    {
        $this->model = new Caixa();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all(array $params = [])
    {
        $sql = "
            SELECT c.id, 
                c.uuid, 
                c.opened_at, 
                c.closed_at, 
                c.initial_amount,
                c.current_balance, 
                c.final_amount, 
                c.difference, 
                c.status, 
                c.obs,
                c.id_usuario_opened
            FROM caixas c
            INNER JOIN usuarios u ON c.id_usuario_opened = u.id
        ";

        $bindings = [];
        $conditions = [];

        if (!empty($params['status'])) {
            $conditions[] = "c.status = :status";
            $bindings[':status'] = $params['status'];
        }

        if (!empty($params['search'])) {
            $conditions[] = "u.name LIKE :search";
            $bindings[':search'] = '%' . $params['search'] . '%';
        }

        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $conditions[] = "c.opened_at BETWEEN :start_date AND :end_date";
            $bindings[':start_date'] = $params['start_date'] . ' 00:00:00';
            $bindings[':end_date'] = $params['end_date'] . ' 23:59:59';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY c.opened_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    private function findCashByUserId(int $userId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM caixas 
            WHERE id_usuario_opened = :user_id 
            AND status = :status
            LIMIT 1
        ");

        $stmt->execute([':user_id' => $userId, ':status' => self::OPEN]);

        return $stmt->fetchObject(Caixa::class);
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $caixaExistente = $this->findCashByUserId($data['id_usuario_opened']);

        if ($caixaExistente) {
            return $caixaExistente;
        }

        try {
            $caixa = $this->model->create($data);

            $stmt = $this->conn->prepare(
                "INSERT INTO caixas SET 
                    uuid=:uuid,
                    id_usuario_opened=:user_id,
                    opened_at=now(),
                    initial_amount=:initial_amount,
                    current_balance=:current_balance,
                    status=:status,
                    obs=:obs
                "
            );

            $stmt->execute([
                ':uuid' => $caixa->uuid,
                ':user_id' => $caixa->id_usuario_opened,
                ':initial_amount' => $caixa->initial_amount,
                ':current_balance' => $caixa->current_balance,
                ':status' => $caixa->status,
                ':obs' => $caixa->obs
            ]);

            return $this->findByUuid($caixa->uuid);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function update(array $data, int $id)
    {
        $caixa = $this->findById($id);
        if (!$caixa) return null;

        $caixa = $this->model->update($data, $caixa);

        $stmt = $this->conn->prepare("
            UPDATE caixas SET 
                status = :status, 
                final_amount = :final_amount,
                id_usuario_closed = :id_usuario_closed,
                closed_at = :closed_at,
                difference = :difference,
                obs = :obs
            WHERE id = :id
        ");

        $stmt->execute([
            ':status' => $caixa->status,
            ':final_amount' => $caixa->final_amount ?? 0,
            ':id_usuario_closed' => $caixa->id_usuario_closed ?? null,
            ':closed_at' => $caixa->closed_at ?? null,
            ':difference' => $caixa->difference ?? 0,
            ':obs' => $caixa->obs ?? null,
            ':id' => $id
        ]);

        return $this->findById($id);
    }

    public function closedCashbox(int $id, array $data)
    {
        // Força o status como 'fechado'
        $data['status'] = self::CLOSED;

        return $this->update($data, $id);
    }

    public function openedCashbox(int $id_usuario)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM caixas 
            WHERE id_usuario_opened = :id_usuario 
            AND status = :status
            LIMIT 1
        ");
        $stmt->execute([':id_usuario' => $id_usuario, ':status' => self::OPEN]);
        $register = $stmt->fetchObject(Caixa::class);

        if (!$register) {
            return null;
        }

        return $register;
    }

    public function delete(int $id)
    {
        $caixa = $this->findById($id);
        if (!$caixa) return false;

        $stmt = $this->conn->prepare("DELETE FROM caixas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateBalance(int $id_caixa, string $type, string $payment_form, float $amount)
    {
        $caixa = $this->findById($id_caixa);
        if (!$caixa) {
            return null;
        }

        if ($type === self::TYPE_INPUT && $payment_form === self::FORM_CASH) {
            $caixa->current_balance += $amount;
        }

        if ($type === self::TYPE_OUTPUT && $payment_form === self::FORM_CASH) {
            $caixa->current_balance -= $amount;
        }

        if ($type === self::TYPE_BLOOD && $payment_form === self::FORM_CASH) {
            $caixa->current_balance -= $amount;
        }

        $stmt = $this->conn->prepare("
            UPDATE caixas SET current_balance = :current_balance WHERE id = :id
        ");

        $updated = $stmt->execute([
            ':current_balance' => $caixa->current_balance,
            ':id' => $id_caixa
        ]);

        if (!$updated) {
            return null;
        }

        return $this->findById($id_caixa);
    }
}
