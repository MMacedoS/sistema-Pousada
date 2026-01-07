<?php

namespace App\Repositories\Entities\Payment;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Payment\Pagamento;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class PagamentoRepository extends SingletonInstance implements IPagamentoRepository
{
    private const CLASS_NAME = Pagamento::class;
    private const TABLE = 'pagamentos';
    private const PAGO = 1;
    private const NAO_PAGO = 0;
    private const IS_NOT_DELETED = 0;
    private const IS_DELETED = 1;

    use FindTrait;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no PagamentoRepository");
            }

            $this->model = new Pagamento();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor PagamentoRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT p.*
                FROM " . self::TABLE . " p 
                LEFT JOIN vendas v ON p.id_venda = v.id 
                LEFT JOIN usuarios u ON p.id_usuario = u.id 
                WHERE p.status = 1";

        $filters = [];

        if (!empty($params['type_payment'])) {
            $sql .= " AND p.type_payment = :type_payment";
            $filters[':type_payment'] = $params['type_payment'];
        }

        if (!empty($params['start_date'])) {
            $sql .= " AND p.dt_payment >= :start_date";
            $filters[':start_date'] = $params['start_date'];
        }

        if (!empty($params['end_date'])) {
            $sql .= " AND p.dt_payment <= :end_date";
            $filters[':end_date'] = $params['end_date'];
        }

        if (!empty($params['id_caixa'])) {
            $sql .= " AND p.id_caixa = :id_caixa";
            $filters[':id_caixa'] = $params['id_caixa'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filters);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data)
    {
        try {
            $pagamento = $this->model->create($data);

            $sql = "INSERT INTO " . self::TABLE . " (uuid, id_reserva, type_payment, payment_amount, dt_payment, id_venda, status, id_usuario, id_caixa) 
                    VALUES (:uuid, :id_reserva, :type_payment, :payment_amount, :dt_payment, :id_venda, :status, :id_usuario, :id_caixa)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $pagamento->uuid,
                ':id_reserva' => $pagamento->id_reserva,
                ':type_payment' => $pagamento->type_payment,
                ':payment_amount' => $pagamento->payment_amount,
                ':dt_payment' => $pagamento->dt_payment,
                ':id_venda' => $pagamento->id_venda,
                ':status' => $pagamento->status,
                ':id_usuario' => $pagamento->id_usuario,
                ':id_caixa' => $pagamento->id_caixa
            ]);

            $pagamento->id = $this->conn->lastInsertId();
            return $pagamento;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar pagamento: " . $e->getMessage());
            throw new \Exception("Erro ao criar pagamento");
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $pagamento = $this->findById($id);
            if (!$pagamento) {
                throw new \Exception("Pagamento não encontrado");
            }

            $updatedPagamento = $this->model->update($data, $pagamento);

            $sql = "UPDATE " . self::TABLE . " SET 
                    id_reserva = :id_reserva, 
                    type_payment = :type_payment, 
                    payment_amount = :payment_amount, 
                    dt_payment = :dt_payment, 
                    id_venda = :id_venda, 
                    status = :status, 
                    id_usuario = :id_usuario, 
                    id_caixa = :id_caixa 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_reserva' => $updatedPagamento->id_reserva,
                ':type_payment' => $updatedPagamento->type_payment,
                ':payment_amount' => $updatedPagamento->payment_amount,
                ':dt_payment' => $updatedPagamento->dt_payment,
                ':id_venda' => $updatedPagamento->id_venda,
                ':status' => $updatedPagamento->status,
                ':id_usuario' => $updatedPagamento->id_usuario,
                ':id_caixa' => $updatedPagamento->id_caixa,
                ':id' => $id
            ]);

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar pagamento: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar pagamento");
        }
    }

    public function delete(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao deletar pagamento: " . $e->getMessage());
            throw new \Exception("Erro ao deletar pagamento");
        }
    }

    public function findByVenda(int $vendaId)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id_venda = :venda_id AND status = 1 ORDER BY created_at ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':venda_id' => $vendaId]);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar pagamentos por venda: " . $e->getMessage());
            return [];
        }
    }

    public function findByReserva(int $reservaId)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id_reserva = :reserva_id AND status = 1 ORDER BY created_at ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':reserva_id' => $reservaId]);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar pagamentos por reserva: " . $e->getMessage());
            return [];
        }
    }

    public function findByCaixa(int $caixaId)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id_caixa = :caixa_id AND status = 1 ORDER BY created_at ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':caixa_id' => $caixaId]);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar pagamentos por caixa: " . $e->getMessage());
            return [];
        }
    }

    public function findByPeriod(string $startDate, string $endDate)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " 
                    WHERE dt_payment BETWEEN :start_date AND :end_date 
                    AND status = 1 
                    ORDER BY dt_payment DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar pagamentos por período: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalByType(string $type)
    {
        try {
            $sql = "SELECT SUM(payment_amount) as total FROM " . self::TABLE . " 
                    WHERE type_payment = :type AND status = 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':type' => $type]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total ?? 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao calcular total por tipo: " . $e->getMessage());
            return 0;
        }
    }

    public function cancelPayment(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 0, is_deleted = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao cancelar pagamento: " . $e->getMessage());
            throw new \Exception("Erro ao cancelar pagamento");
        }
    }

    public function paidAmountByReservaId(int $reservaId): float
    {
        if ($reservaId <= 0) {
            return 0.0;
        }

        try {
            $stmt = $this->conn->prepare(
                "SELECT SUM(payment_amount) as paid_amount FROM " . self::TABLE . " WHERE id_reserva = :reserva_id AND status = 1"
            );
            $stmt->execute([':reserva_id' => $reservaId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result['paid_amount'] !== null ? (float)$result['paid_amount'] : 0.0;
        } catch (\Throwable $th) {
            return 0.0;
        }
    }

    public function getRevenueByPeriod($start, $end)
    {
        $start = $start ?? date('Y-m-d');
        $end = $end ?? date('Y-m-d');

        $stmt = $this->conn->prepare(
            "SELECT 
                dt_payment as date, 
                type_payment, 
                SUM(payment_amount) as revenue
             FROM pagamentos
             WHERE dt_payment BETWEEN :start AND :end
               AND status = :status
               AND is_deleted = :is_deleted
             GROUP BY dt_payment, type_payment
             ORDER BY dt_payment ASC"
        );
        $stmt->execute([
            ':start' => $start,
            ':end' => $end,
            ':status' => self::PAGO,
            ':is_deleted' => self::IS_NOT_DELETED
        ]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response = [];
        foreach ($result as $row) {
            $date = $row['date'];
            $type = $row['type_payment'];
            $revenue = (float)$row['revenue'];

            if (!isset($response[$date])) {
                $response[$date] = [];
            }
            $response[$date][$type] = $revenue;
        }

        $formatted = [];
        foreach ($response as $date => $types) {
            $formatted[] = [
                'date' => $date,
                'types' => $types
            ];
        }

        return $formatted;
    }
}
