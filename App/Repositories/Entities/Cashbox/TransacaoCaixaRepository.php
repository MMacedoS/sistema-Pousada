<?php

namespace App\Repositories\Entities\Cashbox;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Cashbox\TransacaoCaixa;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Repositories\Traits\FindTrait;

class TransacaoCaixaRepository extends SingletonInstance implements ITransacaoCaixaRepository
{
    private const CLASS_NAME = TransacaoCaixa::class;
    private const TABLE = "transacao_caixa";

    use FindTrait;

    public function __construct()
    {
        $this->model = new TransacaoCaixa();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create(array $data)
    {
        $transacao = $this->model->create($data);

        $stmt = $this->conn->prepare("
            INSERT INTO transacao_caixa (
                uuid, caixa_id, type, origin, reference_uuid, description,
                payment_form, canceled, amount, id_usuario, created_at
            ) VALUES (
                :uuid, :caixa_id, :type, :origin, :reference_uuid, :description,
                :payment_form, :canceled, :amount, :id_usuario, NOW()
            )
        ");

        $stmt->execute([
            ':uuid' => $transacao->uuid,
            ':caixa_id' => $transacao->caixa_id,
            ':type' => $transacao->type,
            ':origin' => $transacao->origin,
            ':reference_uuid' => $transacao->reference_uuid ?? null,
            ':description' => $transacao->description,
            ':payment_form' => $transacao->payment_form,
            ':canceled' => $transacao->canceled ?? 0,
            ':amount' => $transacao->amount,
            ':id_usuario' => $transacao->id_usuario ?? null
        ]);

        return $this->findByUuid($transacao->uuid);
    }

    public function byCaixaId(int $caixa_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa WHERE caixa_id = :caixa_id ORDER BY created_at DESC
        ");
        $stmt->execute([':caixa_id' => $caixa_id]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function all(array $params = [])
    {
        $sql = "
            SELECT t.*, 
                   u.name AS usuario_nome,
                   c.status AS caixa_status
            FROM transacao_caixa t
            LEFT JOIN usuarios u ON t.id_usuario = u.id
            LEFT JOIN caixas c ON t.caixa_id = c.id
            WHERE 1 = 1
        ";

        $bindings = [];

        if (!empty($params['type'])) {
            $sql .= " AND t.type = :type";
            $bindings[':type'] = $params['type'];
        }

        if (!empty($params['origin'])) {
            $sql .= " AND t.origin = :origin";
            $bindings[':origin'] = $params['origin'];
        }

        if (!empty($params['data_inicio']) && !empty($params['data_fim'])) {
            $sql .= " AND DATE(t.created_at) BETWEEN :data_inicio AND :data_fim";
            $bindings[':data_inicio'] = $params['data_inicio'];
            $bindings[':data_fim'] = $params['data_fim'];
        }

        if (!empty($params['caixa_id'])) {
            $sql .= " AND t.caixa_id = :caixa_id";
            $bindings[':caixa_id'] = $params['caixa_id'];
        }

        if (!empty($params['payment_form'])) {
            $sql .= " AND t.payment_form = :payment_form";
            $bindings[':payment_form'] = $params['payment_form'];
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function cancelledTransaction(int $id)
    {
        // Buscar a transação
        $transacao = $this->findById($id);
        if (!$transacao || $transacao->canceled) {
            return null; // Já cancelada ou inexistente
        }

        // Cancelar a transação
        $stmt = $this->conn->prepare("
            UPDATE transacao_caixa 
            SET canceled = 1 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);

        // Retornar transação atualizada
        return $this->findById($id);
    }

    public function updateTransaction(array $data, int $id)
    {
        $transacao = $this->findById($id);
        if (!$transacao) {
            return null;
        }

        $updatedTransacao = $this->model->update($data, $transacao);

        $stmt = $this->conn->prepare("
            UPDATE transacao_caixa SET 
                type = :type,
                origin = :origin,
                reference_uuid = :reference_uuid,
                description = :description,
                payment_form = :payment_form,
                amount = :amount,
                id_usuario = :id_usuario
            WHERE id = :id
        ");

        $stmt->execute([
            ':type' => $updatedTransacao->type,
            ':origin' => $updatedTransacao->origin,
            ':reference_uuid' => $updatedTransacao->reference_uuid,
            ':description' => $updatedTransacao->description,
            ':payment_form' => $updatedTransacao->payment_form,
            ':amount' => $updatedTransacao->amount,
            ':id_usuario' => $updatedTransacao->id_usuario,
            ':id' => $id
        ]);

        return $this->findById($id);
    }
}
