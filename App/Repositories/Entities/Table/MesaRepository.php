<?php

namespace App\Repositories\Entities\Table;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Table\Mesa;
use App\Repositories\Contracts\Table\IMesaRepository;
use App\Utils\LoggerHelper;
use PDO;

class MesaRepository extends SingletonInstance implements IMesaRepository
{
    private const CLASS_NAME = Mesa::class;
    private const TABLE = 'mesas';

    protected $conn;
    protected $model;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no MesaRepository");
            }

            $this->model = new Mesa();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor MesaRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT m.*, v.name as venda_nome, v.amount_sale as venda_total 
                FROM " . self::TABLE . " m 
                LEFT JOIN vendas v ON m.id_venda = v.id 
                WHERE 1=1";

        $filters = [];

        if (!empty($params['status'])) {
            $sql .= " AND m.status = :status";
            $filters[':status'] = $params['status'];
        }

        if (!empty($params['search'])) {
            $sql .= " AND m.name LIKE :search";
            $filters[':search'] = '%' . $params['search'] . '%';
        }

        $sql .= " ORDER BY m.name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filters);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create(array $data)
    {
        try {
            $mesa = $this->model->create($data);

            $sql = "INSERT INTO " . self::TABLE . " (uuid, name, status, id_venda) 
                    VALUES (:uuid, :name, :status, :id_venda)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $mesa->uuid,
                ':name' => $mesa->name,
                ':status' => $mesa->status,
                ':id_venda' => $mesa->id_venda
            ]);

            $mesa->id = $this->conn->lastInsertId();
            return $mesa;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao criar mesa");
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
            LoggerHelper::logError("Erro ao buscar mesa por ID: " . $e->getMessage());
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
            LoggerHelper::logError("Erro ao buscar mesa por UUID: " . $e->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $mesa = $this->findById($id);
            if (!$mesa) {
                throw new \Exception("Mesa não encontrada");
            }

            $updatedMesa = $this->model->update($data, $mesa);

            $sql = "UPDATE " . self::TABLE . " SET 
                    name = :name, 
                    status = :status, 
                    id_venda = :id_venda 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':name' => $updatedMesa->name,
                ':status' => $updatedMesa->status,
                ':id_venda' => $updatedMesa->id_venda,
                ':id' => $id
            ]);

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar mesa");
        }
    }

    public function delete(int $id)
    {
        try {
            $sql = "DELETE FROM " . self::TABLE . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao deletar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao deletar mesa");
        }
    }

    public function findByStatus(string $status)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE status = :status ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':status' => $status]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar mesas por status: " . $e->getMessage());
            return [];
        }
    }

    public function occupyTable(int $id, int $vendaId)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 'ocupada', id_venda = :venda_id WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':venda_id' => $vendaId,
                ':id' => $id
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao ocupar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao ocupar mesa");
        }
    }

    public function freeTable(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 'livre', id_venda = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao liberar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao liberar mesa");
        }
    }

    public function closeTable(int $id)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 'fechada' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao fechar mesa: " . $e->getMessage());
            throw new \Exception("Erro ao fechar mesa");
        }
    }

    public function getAvailableTables()
    {
        return $this->findByStatus('livre');
    }

    public function getOccupiedTables()
    {
        return $this->findByStatus('ocupada');
    }
}
