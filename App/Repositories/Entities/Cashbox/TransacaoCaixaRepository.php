<?php

namespace App\Repositories\Entities\Cashbox;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Cashbox\TransacaoCaixa;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use Monolog\Logger;

class TransacaoCaixaRepository extends SingletonInstance implements ITransacaoCaixaRepository
{
    private const CLASS_NAME = TransacaoCaixa::class;
    private const FORM_CASH = 'cash';
    private const TYPE_INPUT = 'entrada';
    private const TYPE_OUTPUT = 'saida';
    private const TYPE_BLOOD = 'sangria';
    private const TABLE = "transacao_caixa";
    private $caixaRepository;

    use FindTrait;

    public function __construct()
    {
        $this->model = new TransacaoCaixa();
        $this->conn = Database::getInstance()->getConnection();
        $this->caixaRepository = CaixaRepository::getInstance();
    }

    public function create(array $data)
    {
        $transacao = $this->model->create($data);

        $this->conn->beginTransaction();
        try {

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

            $created = $this->findByUuid($transacao->uuid);

            $caixaUpdated = $this->caixaRepository->updateBalance(
                (int)$transacao->caixa_id,
                $transacao->type,
                strtolower($transacao->payment_form),
                (float)$transacao->amount
            );

            if (!$caixaUpdated) {
                throw new \Exception('Erro ao atualizar o caixa após a criação da transação');
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();

            return $created;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        $originalTransaction = $this->findById($id);
        if (!$originalTransaction) {
            return null;
        }

        $this->conn->beginTransaction();
        try {
            $transactionLast = $this->findById($id);
            $updatedTransaction = $this->updateTransactionData($data, $originalTransaction, $id);
            $this->adjustCashboxBalance($transactionLast, $updatedTransaction);

            $this->conn->commit();
            return $this->findById($id);
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function updateTransactionData(array $data, $originalTransaction, int $id)
    {
        $updatedTransaction = $this->model->update($data, $originalTransaction);

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
            ':type' => $updatedTransaction->type,
            ':origin' => $updatedTransaction->origin,
            ':reference_uuid' => $updatedTransaction->reference_uuid,
            ':description' => $updatedTransaction->description,
            ':payment_form' => $updatedTransaction->payment_form,
            ':amount' => $updatedTransaction->amount,
            ':id_usuario' => $updatedTransaction->id_usuario,
            ':id' => $id
        ]);

        return $this->findById($id);
    }

    private function adjustCashboxBalance($originalTransaction, $updatedTransaction)
    {
        $caixaId = (int)$originalTransaction->caixa_id;

        $originalPaymentAffectsCash = $this->paymentAffectsCashbox($originalTransaction->payment_form);
        $newPaymentAffectsCash = $this->paymentAffectsCashbox($updatedTransaction->payment_form);

        if (!$originalPaymentAffectsCash && !$newPaymentAffectsCash) {
            return;
        }

        if ($originalPaymentAffectsCash && !$newPaymentAffectsCash) {
            $result = $this->revertCashboxTransaction($caixaId, $originalTransaction);
            return $result;
        }

        if (!$originalPaymentAffectsCash && $newPaymentAffectsCash) {
            $result = $this->applyCashboxTransaction($caixaId, $updatedTransaction);
            return $result;
        }

        if ($originalPaymentAffectsCash && $newPaymentAffectsCash) {
            return $this->handleCashboxAffectingTransactionUpdate($caixaId, $originalTransaction, $updatedTransaction);
        }
    }

    private function paymentAffectsCashbox(string $paymentForm): bool
    {
        $affects = strtolower($paymentForm) === self::FORM_CASH;
        return $affects;
    }

    private function revertCashboxTransaction(int $caixaId, $transaction)
    {
        $revertType = $transaction->type === self::TYPE_INPUT ? self::TYPE_OUTPUT : self::TYPE_INPUT;
        $result = $this->caixaRepository->updateBalance(
            $caixaId,
            $revertType,
            self::FORM_CASH,
            (float)$transaction->amount
        );
        return is_null($result);
    }

    private function applyCashboxTransaction(int $caixaId, $transaction)
    {
        $result = $this->caixaRepository->updateBalance(
            $caixaId,
            $transaction->type,
            self::FORM_CASH,
            (float)$transaction->amount
        );
        return is_null($result);
    }

    private function handleCashboxAffectingTransactionUpdate(int $caixaId, $originalTransaction, $updatedTransaction)
    {
        $oldType = $originalTransaction->type;
        $newType = $updatedTransaction->type;
        $oldAmount = (float)$originalTransaction->amount;
        $newAmount = (float)$updatedTransaction->amount;

        if ($oldType !== $newType) {
            $this->revertCashboxTransaction($caixaId, $originalTransaction);
            $this->applyCashboxTransaction($caixaId, $updatedTransaction);
            return;
        }

        if ($oldAmount !== $newAmount) {
            $amountDifference = $newAmount - $oldAmount;

            $this->caixaRepository->updateBalance(
                $caixaId,
                $newType,
                self::FORM_CASH,
                $amountDifference
            );
        }
        return true;
    }

    public function byCaixaId(int $caixa_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa WHERE caixa_id = :caixa_id and canceled = 0 ORDER BY created_at DESC
        ");
        $stmt->execute([':caixa_id' => $caixa_id]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function all(array $params = [])
    {
        $sql = "
            SELECT t.*                   
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

        if (!empty($params['id_usuario'])) {
            $sql .= " AND t.id_usuario = :id_usuario";
            $bindings[':id_usuario'] = $params['id_usuario'];
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function cancelledTransaction(int $id)
    {
        $transacao = $this->findById($id);
        if (!$transacao || $transacao->canceled) {
            return null;
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("
                UPDATE transacao_caixa SET 
                    canceled = 1,
                    updated_at = NOW()
                WHERE id = :id
            ");

            $stmt->execute([':id' => $id]);

            if ($this->paymentAffectsCashbox($transacao->payment_form)) {
                $revertType = $transacao->type === self::TYPE_INPUT ? self::TYPE_OUTPUT : self::TYPE_INPUT;
                $result = $this->caixaRepository->updateBalance(
                    (int)$transacao->caixa_id,
                    $revertType,
                    self::FORM_CASH,
                    (float)$transacao->amount
                );

                if (!$result) {
                    throw new \Exception('Erro ao atualizar o caixa após o cancelamento da transação');
                }
            }

            $this->conn->commit();
            return $this->findById($id);
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
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

    public function findByCashboxIdAndType(int $caixaId, string $type, string $payment_form = self::FORM_CASH)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa
            WHERE caixa_id = :caixa_id AND type = :type AND payment_form = :payment_form
        ");
        $stmt->execute([
            ':caixa_id' => $caixaId,
            ':type' => $type,
            ':payment_form' => $payment_form
        ]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function transactionsByUserId(int $id_usuario)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa
            WHERE id_usuario = :id_usuario
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function findByPeriod(string $startDate, string $endDate)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            ORDER BY created_at DESC
        ");
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function findByPeriodAndType(string $startDate, string $endDate, string $type)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM transacao_caixa
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            AND type = :type
            ORDER BY created_at DESC
        ");
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':type' => $type
        ]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }
}
