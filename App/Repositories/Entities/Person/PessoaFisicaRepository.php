<?php

namespace App\Repositories\Entities\Person;

use App\Config\Database;
use App\Models\Person\PessoaFisica;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PessoaFisicaRepository implements IPessoaFisicaRepository {
    const CLASS_NAME = PessoaFisica::class;
    const TABLE = 'pessoa_fisica';
    
    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new PessoaFisica();
    }

    public function allPersons()
    {
        $stmt = $this->conn->query(
        "SELECT 
           p.*
            FROM " . self::TABLE . " p 
        ");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function personByUserId(int $user_id)
    {
        $stmt = $this->conn->query(
            "SELECT 
            p.*
            FROM " . self::TABLE . " p 
            WHERE p.usuario_id = '$user_id'
            LIMIT 1
            "
        );

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  
        if (is_null($register)) {
            return null;
        }
    
        return $register;       
    }

    public function create(array $data)
    {
        $existingPerson = $this->findPessoaFisica($data);

        if (!is_null($existingPerson)) {
            return $existingPerson;
        }

        try {
            $pessoa_fisica = $this->model->create($data);
            
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    usuario_id = :usuario_id,
                    nome = :nome,
                    nome_social = :nome_social,
                    email = :email,
                    data_nascimento = :data_nascimento,
                    cpf = :cpf,
                    rg = :rg,
                    nome_mae = :nome_mae,
                    nome_pai = :nome_pai,
                    telefone = :telefone,
                    endereco = :endereco
                    "
            );
    
            $create = $stmt->execute([
                ':uuid' => $pessoa_fisica->uuid,
                ':usuario_id' => $pessoa_fisica->usuario_id,
                ':nome' => $pessoa_fisica->nome,
                ':nome_social' => $pessoa_fisica->nome_social,
                ':email' => $pessoa_fisica->email,
                ':data_nascimento' => $pessoa_fisica->data_nascimento,
                ':nome_pai' => $pessoa_fisica->nome_pai,
                ':nome_mae' => $pessoa_fisica->nome_mae,
                ':telefone' => $pessoa_fisica->telefone,
                ':rg' => $pessoa_fisica->rg,
                ':endereco' => $pessoa_fisica->endereco,
                ':cpf' => $pessoa_fisica->cpf
            ]);
            
            if (!$create) {
                return null;
            }
    
            return $this->findByUuid($pessoa_fisica->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }    

    public function update(array $data, int $id)
    {
        $pessoa_fisica = $this->findById($id);

        if (is_null($pessoa_fisica)) {
            return null;
        }

        $pessoa_fisica = $this->model->update(
            $data,
            $pessoa_fisica
        );
        
        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                    usuario_id = :usuario_id,
                    nome = :nome,
                    nome_social = :nome_social,
                    email = :email,
                    data_nascimento = :data_nascimento,
                    cpf = :cpf,
                    rg = :rg,
                    nome_mae = :nome_mae,
                    nome_pai = :nome_pai,
                    telefone = :telefone,
                    endereco = :endereco,
                    updated_at = NOW()
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':usuario_id' => $pessoa_fisica->usuario_id,
                ':nome' => $pessoa_fisica->nome,
                ':nome_social' => $pessoa_fisica->nome_social,
                ':email' => $pessoa_fisica->email,
                ':data_nascimento' => $pessoa_fisica->data_nascimento,
                ':nome_pai' => $pessoa_fisica->nome_pai,
                ':nome_mae' => $pessoa_fisica->nome_mae,
                ':telefone' => $pessoa_fisica->telefone,
                ':rg' => $pessoa_fisica->rg,
                ':endereco' => $pessoa_fisica->endereco,
                ':cpf' => $pessoa_fisica->cpf
            ]);

            if (!$updated) {        
                return null;
            }
            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function findPessoaFisica(array $criteria): ?PessoaFisica
    {
        try {
            $conditions = [];
            $params = [];
            if (!empty($criteria['name'])) {
                $conditions[] = "nome = :nome";
                $params[':nome'] = $criteria['name'];
            }
            if (!empty($criteria['email'])) {
                $conditions[] = "email = :email";
                $params[':email'] = $criteria['email'];
            }
            if (!empty($criteria['cpf'])) {
                $conditions[] = "cpf = :cpf";
                $params[':cpf'] = $criteria['cpf'];
            }

            if (empty($conditions)) {
                return null; 
            }

            $sql = "SELECT * FROM " . self::TABLE . " WHERE " . implode(' AND ', $conditions);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function delete(int $id) 
    {
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . " 
             SET ativo = 0 
             WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }

    public function remove($id) :?bool 
    {
        
        $pessoa_fisica = $this->findById((int)$id);
       
        if (is_null($pessoa_fisica)) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if($delete) {
                return true;
            }
            return false;
        } catch(\Throwable $th) {
            dd($th->getMessage());
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}