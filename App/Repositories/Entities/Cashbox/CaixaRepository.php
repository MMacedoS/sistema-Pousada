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
            INNER JOIN pessoa_fisica pf ON u.id = pf.usuario_id 
        ";

        $bindings = [];
        $conditions = [];

        if (!empty($params['status'])) {
            $conditions[] = "c.status = :status";
            $bindings[':status'] = $params['status'];
        }

        if (!empty($params['nome'])) {
            $conditions[] = "pf.name LIKE :nome";
            $bindings[':nome'] = '%' . $params['nome'] . '%';
        }

        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $conditions[] = "c.opened_at BETWEEN :start_date AND :end_date";
            $bindings[':start_date'] = $params['start_date'];
            $bindings[':end_date'] = $params['end_date'];
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
            AND status = 'aberto'
            LIMIT 1
        ");

        $stmt->execute([':user_id' => $userId]);

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
                obs = :obs
            WHERE id = :id
        ");

        $stmt->execute([
            ':status' => $caixa->status,
            ':final_amount' => $caixa->final_amount ?? 0,
            ':id_usuario_closed' => $caixa->id_usuario_closed ?? null,
            ':closed_at' => $caixa->closed_at ?? null,
            ':obs' => $caixa->obs ?? null,
            ':id' => $id
        ]);

        return $this->findById($id);
    }

    public function closedCashbox(int $id, array $data)
    {
        // Força o status como 'fechado'
        $data['status'] = 'fechado';

        return $this->update($data, $id);
    }

    public function openedCashbox(int $id_usuario)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM caixas 
            WHERE id_usuario_opened = :id_usuario 
            AND status = 'aberto' 
            LIMIT 1
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchObject(Caixa::class);
    }

    public function delete(int $id)
    {
        $caixa = $this->findById($id);
        if (!$caixa) return false;

        $stmt = $this->conn->prepare("DELETE FROM caixas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
