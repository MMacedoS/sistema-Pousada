<?php

namespace App\Repositories\Entities\Sale;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Sale\ItemVenda;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Utils\LoggerHelper;
use PDO;

class ItemVendaRepository extends SingletonInstance implements IItemVendaRepository
{
    private const CLASS_NAME = ItemVenda::class;
    private const TABLE = 'itens_venda';

    protected $conn;
    protected $model;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no ItemVendaRepository");
            }

            $this->model = new ItemVenda();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor ItemVendaRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT iv.*, p.name as produto_nome, p.price as produto_preco 
                FROM " . self::TABLE . " iv 
                LEFT JOIN produtos p ON iv.id_produto = p.id 
                WHERE iv.status = 1";

        $filters = [];

        if (!empty($params['id_venda'])) {
            $sql .= " AND iv.id_venda = :id_venda";
            $filters[':id_venda'] = $params['id_venda'];
        }

        if (!empty($params['id_produto'])) {
            $sql .= " AND iv.id_produto = :id_produto";
            $filters[':id_produto'] = $params['id_produto'];
        }

        $sql .= " ORDER BY iv.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filters);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create(array $data)
    {
        try {
            $item = $this->model->create($data);

            $sql = "INSERT INTO " . self::TABLE . " (uuid, id_venda, id_produto, status, quantity, amount_item, id_usuario) 
                    VALUES (:uuid, :id_venda, :id_produto, :status, :quantity, :amount_item, :id_usuario)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $item->uuid,
                ':id_venda' => $item->id_venda,
                ':id_produto' => $item->id_produto,
                ':status' => $item->status,
                ':quantity' => $item->quantity,
                ':amount_item' => $item->amount_item,
                ':id_usuario' => $item->id_usuario
            ]);

            $item->id = $this->conn->lastInsertId();
            return $item;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar item de venda: " . $e->getMessage());
            throw new \Exception("Erro ao criar item de venda");
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
            LoggerHelper::logError("Erro ao buscar item por ID: " . $e->getMessage());
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
            LoggerHelper::logError("Erro ao buscar item por UUID: " . $e->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $item = $this->findById($id);
            if (!$item) {
                throw new \Exception("Item de venda não encontrado");
            }

            $updatedItem = $this->model->update($data, $item);

            $sql = "UPDATE " . self::TABLE . " SET 
                    id_venda = :id_venda, 
                    id_produto = :id_produto, 
                    status = :status, 
                    quantity = :quantity, 
                    amount_item = :amount_item, 
                    id_usuario = :id_usuario 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_venda' => $updatedItem->id_venda,
                ':id_produto' => $updatedItem->id_produto,
                ':status' => $updatedItem->status,
                ':quantity' => $updatedItem->quantity,
                ':amount_item' => $updatedItem->amount_item,
                ':id_usuario' => $updatedItem->id_usuario,
                ':id' => $id
            ]);

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar item de venda: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar item de venda");
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
            LoggerHelper::logError("Erro ao deletar item de venda: " . $e->getMessage());
            throw new \Exception("Erro ao deletar item de venda");
        }
    }

    public function findByVenda(int $vendaId)
    {
        try {
            $sql = "SELECT iv.*, p.name as produto_nome, p.price as produto_preco 
                    FROM " . self::TABLE . " iv 
                    LEFT JOIN produtos p ON iv.id_produto = p.id 
                    WHERE iv.id_venda = :venda_id AND iv.status = 1 
                    ORDER BY iv.created_at ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':venda_id' => $vendaId]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar itens por venda: " . $e->getMessage());
            return [];
        }
    }

    public function findByProduto(int $produtoId)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id_produto = :produto_id AND status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':produto_id' => $produtoId]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar itens por produto: " . $e->getMessage());
            return [];
        }
    }

    public function removeFromSale(int $vendaId, int $produtoId)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = 0 WHERE id_venda = :venda_id AND id_produto = :produto_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':venda_id' => $vendaId,
                ':produto_id' => $produtoId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao remover item da venda: " . $e->getMessage());
            throw new \Exception("Erro ao remover item da venda");
        }
    }

    public function updateQuantity(int $id, int $quantity)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET quantity = :quantity WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':quantity' => $quantity,
                ':id' => $id
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar quantidade: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar quantidade");
        }
    }

    public function getTotalByVenda(int $vendaId)
    {
        try {
            $sql = "SELECT SUM(amount_item * quantity) as total FROM " . self::TABLE . " 
                    WHERE id_venda = :venda_id AND status = 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':venda_id' => $vendaId]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total ?? 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao calcular total da venda: " . $e->getMessage());
            return 0;
        }
    }

    public function updateStatusByVenda(int $vendaId, int $status)
    {
        try {
            $sql = "UPDATE " . self::TABLE . " SET status = :status WHERE id_venda = :venda_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':venda_id' => $vendaId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar status dos itens da venda: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar status dos itens da venda");
        }
    }
}
