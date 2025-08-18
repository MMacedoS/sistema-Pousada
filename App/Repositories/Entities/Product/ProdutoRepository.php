<?php

namespace App\Repositories\Entities\Product;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Product\Produto;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Utils\LoggerHelper;
use PDO;

class ProdutoRepository extends SingletonInstance implements IProdutoRepository
{
    private const CLASS_NAME = Produto::class;
    private const TABLE = 'produtos';

    protected $conn;
    protected $model;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no ProdutoRepository");
            }

            $this->model = new Produto();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor ProdutoRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT p.*, e.quantity as estoque_quantity 
                FROM " . self::TABLE . " p 
                LEFT JOIN estoque e ON p.id = e.id_produto 
                WHERE p.status = 1";

        $filters = [];

        if (!empty($params['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $filters[':search'] = '%' . $params['search'] . '%';
        }

        if (!empty($params['category'])) {
            $sql .= " AND p.category = :category";
            $filters[':category'] = $params['category'];
        }

        if (!empty($params['status'])) {
            $sql .= " AND p.status = :status";
            $filters[':status'] = $params['status'];
        }

        $sql .= " ORDER BY p.name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($filters);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create(array $data)
    {
        try {
            $produto = $this->model->create($data);

            $sql = "INSERT INTO " . self::TABLE . " (uuid, name, description, price, category, stock, status, id_usuario) 
                    VALUES (:uuid, :name, :description, :price, :category, :stock, :status, :id_usuario)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $produto->uuid,
                ':name' => $produto->name,
                ':description' => $produto->description,
                ':price' => $produto->price,
                ':category' => $produto->category,
                ':stock' => $produto->stock,
                ':status' => $produto->status,
                ':id_usuario' => $produto->id_usuario
            ]);

            $produto->id = $this->conn->lastInsertId();

            if ($produto->stock > 0) {
                $this->createStock($produto->id, $produto->stock);
            }

            return $produto;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar produto: " . $e->getMessage());
            throw new \Exception("Erro ao criar produto");
        }
    }

    private function createStock(int $produtoId, int $quantity)
    {
        try {
            $sql = "INSERT INTO estoque (id_produto, quantity) VALUES (:id_produto, :quantity)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_produto' => $produtoId,
                ':quantity' => $quantity
            ]);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao criar estoque: " . $e->getMessage());
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
            LoggerHelper::logError("Erro ao buscar produto por ID: " . $e->getMessage());
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
            LoggerHelper::logError("Erro ao buscar produto por UUID: " . $e->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $produto = $this->findById($id);
            if (!$produto) {
                throw new \Exception("Produto não encontrado");
            }

            $updatedProduto = $this->model->update($data, $produto);

            $sql = "UPDATE " . self::TABLE . " SET 
                    name = :name, 
                    description = :description, 
                    price = :price, 
                    category = :category, 
                    stock = :stock, 
                    status = :status, 
                    id_usuario = :id_usuario 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':name' => $updatedProduto->name,
                ':description' => $updatedProduto->description,
                ':price' => $updatedProduto->price,
                ':category' => $updatedProduto->category,
                ':stock' => $updatedProduto->stock,
                ':status' => $updatedProduto->status,
                ':id_usuario' => $updatedProduto->id_usuario,
                ':id' => $id
            ]);

            return $this->findById($id);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar produto: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar produto");
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
            LoggerHelper::logError("Erro ao deletar produto: " . $e->getMessage());
            throw new \Exception("Erro ao deletar produto");
        }
    }

    public function findByCategory(string $category)
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE category = :category AND status = 1 ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':category' => $category]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar produtos por categoria: " . $e->getMessage());
            return [];
        }
    }

    public function findActive()
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE status = 1 ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar produtos ativos: " . $e->getMessage());
            return [];
        }
    }

    public function findInStock()
    {
        try {
            $sql = "SELECT p.*, e.quantity as estoque_quantity 
                    FROM " . self::TABLE . " p 
                    INNER JOIN estoque e ON p.id = e.id_produto 
                    WHERE p.status = 1 AND e.quantity > 0 
                    ORDER BY p.name ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar produtos em estoque: " . $e->getMessage());
            return [];
        }
    }

    public function updateStock(int $id, int $quantity)
    {
        try {
            $sql = "UPDATE estoque SET quantity = :quantity WHERE id_produto = :id_produto";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':quantity' => $quantity,
                ':id_produto' => $id
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao atualizar estoque: " . $e->getMessage());
            throw new \Exception("Erro ao atualizar estoque");
        }
    }

    public function checkStock(int $id, int $quantity)
    {
        try {
            $sql = "SELECT quantity FROM estoque WHERE id_produto = :id_produto";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_produto' => $id]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $currentStock = $result->quantity ?? 0;

            return $currentStock >= $quantity;
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao verificar estoque: " . $e->getMessage());
            return false;
        }
    }

    public function getCategories()
    {
        try {
            $sql = "SELECT DISTINCT category FROM " . self::TABLE . " WHERE status = 1 ORDER BY category ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar categorias: " . $e->getMessage());
            return [];
        }
    }

    public function findAvailable()
    {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE status = 1 AND (stock > 0 OR stock IS NULL) ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            LoggerHelper::logError("Erro ao buscar produtos disponíveis: " . $e->getMessage());
            return [];
        }
    }
}
