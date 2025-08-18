<?php

namespace App\Repositories\Entities\Sale;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Sale\Venda;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class VendaRepository extends SingletonInstance implements IVendaRepository
{
    private const CLASS_NAME = Venda::class;
    private const TABLE = 'vendas';

    use FindTrait;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no VendaRepository");
            }

            $this->model = new Venda();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor VendaRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT v.*, u.name as usuario_nome, r.uuid as reserva_uuid 
                FROM " . self::TABLE . " v 
                LEFT JOIN usuarios u ON v.id_usuario = u.id 
                LEFT JOIN reservas r ON v.id_reserva = r.id 
                WHERE v.status = 1";

        $filters = [];

        if (!empty($params['search'])) {
            $sql .= " AND (v.name LIKE :search OR v.description LIKE :search)";
            $filters[':search'] = '%' . $params['search'] . '%';
        }

        if (!empty($params['status'])) {
            $sql .= " AND v.status = :status";
            $filters[':status'] = $params['status'];
        }

        if (!empty($params['usuario_id'])) {
            $sql .= " AND v.id_usuario = :usuario_id";
            $filters[':usuario_id'] = $params['usuario_id'];
        }

        if (!empty($params['start_date'])) {
            $sql .= " AND v.dt_sale >= :start_date";
            $filters[':start_date'] = $params['start_date'];
        }

        if (!empty($params['end_date'])) {
            $sql .= " AND v.dt_sale <= :end_date";
            $filters[':end_date'] = $params['end_date'];
        }

        $sql .= " ORDER BY v.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filters);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create(array $data)
    {
        try {
            $venda = $this->model->create($data);

            $sql = "INSERT INTO " . self::TABLE . " (uuid, dt_sale, name, description, amount_sale, status, id_reserva, id_usuario) 
                    VALUES (:uuid, :dt_sale, :name, :description, :amount_sale, :status, :id_reserva, :id_usuario)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $venda->uuid,
                ':dt_sale' => $venda->dt_sale,
                ':name' => $venda->name,
                ':description' => $venda->description,
                ':amount_sale' => $venda->amount_sale,
                ':status' => $venda->status,
                ':id_reserva' => $venda->id_reserva,
                ':id_usuario' => $venda->id_usuario
            ]);

            $venda->id = $this->conn->lastInsertId();
            return $venda;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar venda: " . $e->getMessage());
            throw new \Exception("Erro ao criar venda");
        }
    }

    public function findById(int $id)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->model->create($data) : null;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar venda por ID: " . $e->getMessage());
            return null;
        }
    }

    public function findByUuid(string $uuid)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE uuid = :uuid";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':uuid' => $uuid]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->model->create($data) : null;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar venda por UUID: " . $e->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $venda = $this->findById($id);
            if (!$venda) {
                throw new \Exception("Venda não encontrada");
            }

            $updatedVenda = $this->model->update($data, $venda);

            $sql = "UPDATE " . self::TABLE . " SET 
                    dt_sale = :dt_sale, 
                    name = :name, 
                    description = :description, 
                    amount_sale = :amount_sale, 
                    status = :status, 
                    id_reserva = :id_reserva, 
                    id_usuario = :id_usuario 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':dt_sale' => $updatedVenda->dt_sale,
                ':name' => $updatedVenda->name,
                ':description' => $updatedVenda->description,
                ':amount_sale' => $updatedVenda->amount_sale,
                ':status' => $updatedVenda->status,
                ':id_reserva' => $updatedVenda->id_reserva,
                ':id_usuario' => $updatedVenda->id_usuario,
                ':id' => $id
            ]);

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar venda: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar venda");
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
            LoggerHelper::logError("Erro ao deletar venda: " . $e->getMessage());
            throw new \Exception("Erro ao deletar venda");
        }
    }

    public function findByMesa(int $mesaId)
    {
        try {
            $sql = "SELECT v.* FROM " . self::TABLE . " v 
                    INNER JOIN mesas m ON m.id_venda = v.id 
                    WHERE m.id = :mesa_id AND v.status = 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':mesa_id' => $mesaId]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar venda por mesa: " . $e->getMessage());
            return null;
        }
    }

    public function findByUsuario(int $usuarioId)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id_usuario = :usuario_id AND status = 1 ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar vendas por usuário: " . $e->getMessage());
            return [];
        }
    }

    public function findByPeriod(string $startDate, string $endDate)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " 
                    WHERE dt_sale BETWEEN :start_date AND :end_date 
                    AND status = 1 
                    ORDER BY dt_sale DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar vendas por período: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalSales(array $filters = [])
    {
        try {
            $sql = "SELECT SUM(amount_sale) as total FROM " . self::TABLE . " WHERE status = 1";

            if (!empty($filters['start_date'])) {
                $sql .= " AND dt_sale >= :start_date";
            }

            if (!empty($filters['end_date'])) {
                $sql .= " AND dt_sale <= :end_date";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($filters);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total ?? 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao calcular total de vendas: " . $e->getMessage());
            return 0;
        }
    }

    public function closeSale(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 2 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao fechar venda: " . $e->getMessage());
            throw new \Exception("Erro ao fechar venda");
        }
    }

    public function cancelSale(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao cancelar venda: " . $e->getMessage());
            throw new \Exception("Erro ao cancelar venda");
        }
    }
}
