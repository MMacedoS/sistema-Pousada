<?php

namespace App\Repositories\Entities\Customer;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Customer\Cliente;
use App\Repositories\Contracts\Customer\IClienteRepository;
use App\Repositories\Entities\Person\PessoaFisicaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use Monolog\Logger;

class ClienteRepository extends SingletonInstance implements IClienteRepository
{
    private const CLASS_NAME = Cliente::class;
    private const TABLE = "clientes";
    use FindTrait;
    private $pessoaFisicaRepository;

    public function __construct()
    {
        $this->model = new Cliente();
        $this->conn = Database::getInstance()->getConnection();
        $this->pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT c.* FROM " . self::TABLE . " c INNER JOIN pessoa_fisica pf ON c.pessoa_fisica_id = pf.id";

        $conditions = [];
        $bindings = [];

        if (isset($params['name'])) {
            $conditions[] = 'pf.name = :name';
            $bindings[':name'] = $params['name'];
        }

        if (isset($params['company'])) {
            $conditions[] = 'c.company = :company';
            $bindings[':company'] = $params['company'];
        }

        if (isset($params['active'])) {
            $conditions[] = 'c.active = :active';
            $bindings[':active'] = $params['active'];
        }

        $conditions[] = 'c.is_deleted = :is_deleted';
        $bindings[':is_deleted'] = $params['is_deleted'] ?? 0;


        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {

            throw new \Exception("Database query error: Cliente " . $e->getMessage());
        }
    }

    public function savePersonAndCustomer(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $this->conn->beginTransaction();

        try {
            $pessoaFisica = $this->pessoaFisicaRepository->create($data);
            if (is_null($pessoaFisica)) {
                $this->conn->rollBack();
                return null;
            }

            $data['pessoa_fisica_id'] = $pessoaFisica->id;

            $existingClient = $this->existingClientByParams(['pessoa_fisica_id' => $data['pessoa_fisica_id']]);

            if (!is_null($existingClient)) {
                $this->conn->rollBack();
                return $existingClient;
            }

            $cliente = $this->create($data);

            if (is_null($cliente)) {
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();
            return $cliente;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Cliente: " . $th->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function create(array $cliente)
    {
        if (empty($cliente)) {
            return null;
        }

        $clienteObj = $this->model->create($cliente);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " SET 
                uuid = :uuid,
                pessoa_fisica_id = :pessoa_fisica_id,
                job = :job,
                nationality = :nationality,
                doc = :doc,
                type_doc = :type_doc,
                representative = :representative,
                company = :company,
                cnpj_company = :cnpj_company,
                phone_company = :phone_company,
                email_company = :email_company
                "
            );

            $created = $stmt->execute([
                ':uuid' => $clienteObj->uuid,
                ':pessoa_fisica_id' => $clienteObj->pessoa_fisica_id,
                ':job' => $clienteObj->job,
                ':nationality' => $clienteObj->nationality,
                ':doc' => $clienteObj->doc,
                ':type_doc' => $clienteObj->type_doc,
                ':representative' => $clienteObj->representative,
                ':company' => $clienteObj->company,
                ':cnpj_company' => $clienteObj->cnpj_company,
                ':phone_company' => $clienteObj->phone_company,
                ':email_company' => $clienteObj->email_company
            ]);

            if (!$created) {
                return null;
            }

            return $clienteObj;
        } catch (\Throwable $th) {

            LoggerHelper::logInfo("Cliente: " . $th->getMessage());
            return null;
        }
    }

    public function updatePersonCustomer(array $data, int $id)
    {
        if (empty($data) || empty($id)) {
            return null;
        }

        $this->conn->beginTransaction();

        try {
            $pessoaFisica = $this->pessoaFisicaRepository->update($data, $data['pessoa_fisica_id']);
            if (is_null($pessoaFisica)) {
                $this->conn->rollBack();
                return null;
            }

            $cliente = $this->update($data, $id);

            if (is_null($cliente)) {
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();
            return $cliente;
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            return null;
        }
    }

    public function update(array $params, int $id)
    {
        if (empty($params) || empty($id)) {
            return null;
        }

        $cliente = $this->findById($id);

        $clienteObj = $this->model->update($params, $cliente);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " SET 
                uuid = :uuid,
                pessoa_fisica_id = :pessoa_fisica_id,
                job = :job,
                nationality = :nationality,
                doc = :doc,
                type_doc = :type_doc,
                representative = :representative,
                company = :company,
                cnpj_company = :cnpj_company,
                phone_company = :phone_company,
                email_company = :email_company
                WHERE id = :id
                "
            );

            $updated = $stmt->execute([
                ':uuid' => $clienteObj->uuid,
                ':pessoa_fisica_id' => $clienteObj->pessoa_fisica_id,
                ':job' => $clienteObj->job,
                ':nationality' => $clienteObj->nationality,
                ':doc' => $clienteObj->doc,
                ':type_doc' => $clienteObj->type_doc,
                ':representative' => $clienteObj->representative,
                ':company' => $clienteObj->company,
                ':cnpj_company' => $clienteObj->cnpj_company,
                ':phone_company' => $clienteObj->phone_company,
                ':email_company' => $clienteObj->email_company,
                ':id' => $id
            ]);

            if (!$updated) {
                return null;
            }

            return $clienteObj;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function existingClientByParams(array $criteria = [])
    {
        if (empty($criteria)) {
            return null;
        }

        try {
            $conditions = [];
            $params = [];

            if (!empty($criteria['pessoa_fisica_id'])) {
                $conditions[] = "pessoa_fisica_id = :pessoa_fisica_id";
                $params[':pessoa_fisica_id'] = $criteria['pessoa_fisica_id'];
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
        }
    }

    public function delete(string $id): bool
    {
        if (empty($id)) {
            return false;
        }

        if (!$this->findById($id)) {
            return false;
        }

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " SET is_deleted = 1 WHERE id = :id"
            );

            $deleted = $stmt->execute([':id' => $id]);

            if (!$deleted) {
                $this->conn->rollBack();
                return false;
            }

            $this->conn->commit();
            return true;
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            return false;
        }
    }
}
