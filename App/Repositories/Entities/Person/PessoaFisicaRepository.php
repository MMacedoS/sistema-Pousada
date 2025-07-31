<?php

namespace App\Repositories\Entities\Person;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Person\PessoaFisica;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PessoaFisicaRepository extends SingletonInstance implements IPessoaFisicaRepository {
    private const CLASS_NAME = PessoaFisica::class;
    private const TABLE = 'pessoa_fisica';
    
    use FindTrait;

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
            WHERE p.usuario_id = $user_id
            LIMIT 1
            "
        );

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  

        if ($register == false) {
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
                    name = :nome,
                    social_name = :nome_social,
                    email = :email,
                    birthday = :data_nascimento,
                    doc = :doc,
                    type_doc = :type_doc,
                    phone = :telefone,
                    address = :endereco
                    "
            );
    
            $create = $stmt->execute([
                ':uuid' => $pessoa_fisica->uuid,
                ':usuario_id' => $pessoa_fisica->usuario_id,
                ':nome' => $pessoa_fisica->name,
                ':nome_social' => $pessoa_fisica->social_name,
                ':email' => $pessoa_fisica->email,
                ':data_nascimento' => $pessoa_fisica->birthday,
                ':telefone' => $pessoa_fisica->phone,
                ':type_doc' => $pessoa_fisica->type_doc,
                ':endereco' => $pessoa_fisica->address,
                ':doc' => $pessoa_fisica->doc
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
                    name = :name,
                    social_name = :social_name,
                    email = :email,
                    birthday = :birthday,
                    doc = :doc,
                    type_doc = :type_doc,
                    phone = :phone,
                    address = :address,
                    updated_at = NOW()
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':usuario_id' => $pessoa_fisica->usuario_id,
                ':name' => $pessoa_fisica->name,
                ':social_name' => $pessoa_fisica->social_name,
                ':email' => $pessoa_fisica->email,
                ':birthday' => $pessoa_fisica->birthday,
                ':phone' => $pessoa_fisica->phone,
                ':type_doc' => $pessoa_fisica->type_doc,
                ':address' => $pessoa_fisica->address,
                ':doc' => $pessoa_fisica->doc
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

    public function updateByUser(array $data, int $user_id)
    {
        $pessoa_fisica = $this->findPessoaFisica(['user_id' => $user_id]);

        if (is_null($pessoa_fisica)) {
            return null;
        }

        return $this->update($data, $pessoa_fisica->id);
    }

    public function findPessoaFisica(array $criteria = []): ?PessoaFisica
    {
        if(empty($criteria)) {
            return null;
        }

        try {
            $conditions = [];
            $params = [];
            if (!empty($criteria['name'])) {
                $conditions[] = "name = :name";
                $params[':name'] = $criteria['name'];
            }
            if (!empty($criteria['email'])) {
                $conditions[] = "email = :email";
                $params[':email'] = $criteria['email'];
            }
            if (!empty($criteria['doc'])) {
                $conditions[] = "doc = :doc";
                $params[':doc'] = $criteria['doc'];
            }

            if (!empty($criteria['user_id'])) {
                $conditions[] = "usuario_id = :user_id";
                $params[':user_id'] = $criteria['user_id'];
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
             SET active = 0 
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