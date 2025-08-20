<?php

namespace App\Repositories\Entities\Apartments;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Apartment\Apartamento;
use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Repositories\Traits\FindTrait;

class ApartamentoRepository extends SingletonInstance implements IApartamentoRepository
{
    private const CLASS_NAME = Apartamento::class;
    private const TABLE = "apartamentos";
    use FindTrait;

    public function __construct()
    {
        $this->model = new Apartamento();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM apartamentos ";

        $conditions = [];
        $bindings = [];

        if (isset($params['name'])) {
            $conditions[] = 'name = :name';
            $bindings[':name'] = $params['name'];
        }

        if (isset($params['category'])) {
            $conditions[] = 'category = :category';
            $bindings[':category'] = $params['category'];
        }

        if (isset($params['active'])) {
            $conditions[] = 'active = :active';
            $bindings[':active'] = $params['active'];
        }

        $conditions[] = 'is_deleted = :is_deleted';
        $bindings[':is_deleted'] = $params['is_deleted'] ?? 0;


        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {

            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function apartmentsAvailable(array $params = [])
    {
        $sql = "SELECT a.*
                FROM apartamentos a
                LEFT JOIN reservas r 
                ON a.id = r.id_apartamento
                AND (
                    r.dt_checkin < :end_date
                    AND r.dt_checkout > :start_date
                )
                WHERE r.id_apartamento IS NULL";

        $bindings = [
            ':start_date' => $params['start_date'],
            ':end_date'   => $params['end_date'],
        ];

        $conditions = [];

        if (isset($params['name'])) {
            $conditions[] = 'a.name = :name';
            $bindings[':name'] = $params['name'];
        }

        if (isset($params['category'])) {
            $conditions[] = 'a.category = :category';
            $bindings[':category'] = $params['category'];
        }

        if (isset($params['active'])) {
            $conditions[] = 'a.active = :active';
            $bindings[':active'] = $params['active'];
        }

        if (!empty($conditions)) {
            $sql .= ' AND ' . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY a.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(\PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $apartment = $this->model->create($data);

            $stmt = $this->conn->prepare(
                "INSERT INTO apartamentos SET 
                uuid = :uuid,
                name = :name,
                description = :description,
                category = :category,
                active = :active,
                situation = :situation
                "
            );
            $stmt->execute([
                ':uuid' => $apartment->uuid,
                ':name' => $apartment->name,
                ':description' => $apartment->description,
                ':category' => $apartment->category,
                ':active' => $apartment->active,
                ':situation' => $apartment->situation
            ]);

            $created = $this->findByUuid($apartment->uuid);

            if (is_null($created)) {
                return null;
            }

            return $created;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        if (empty($data)) {
            return null;
        }

        $apartment = $this->findById($id);

        if (is_null($apartment)) {
            return null;
        }

        try {
            $apartment = $this->model->update($data, $apartment);

            $stmt = $this->conn->prepare(
                "UPDATE apartamentos SET 
                    name = :name,
                    description = :description,
                    category = :category,
                    active = :active,
                    situation = :situation
                WHERE 
                    id = :id
                "
            );
            $created = $stmt->execute([
                ':name' => $apartment->name,
                ':description' => $apartment->description,
                ':category' => $apartment->category,
                ':active' => $apartment->active,
                ':situation' => $apartment->situation,
                ':id' => $id
            ]);

            if ($created) {
                return $this->findById($id);
            }

            return null;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return null;
        }
    }

    public function delete(int $id)
    {
        if (is_null($id)) {
            return null;
        }

        $apartment = $this->findById($id);

        if (is_null($apartment)) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare("UPDATE apartamentos SET is_deleted = 1 WHERE id = :id");

            return $stmt->execute([':id' => $id]) ? $apartment : null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public function changeActiveStatus(int $id)
    {
        if (is_null($id)) {
            return null;
        }

        $apartment = $this->findById($id);

        if (is_null($apartment)) {
            return null;
        }

        $stmt = $this->conn->prepare("UPDATE apartamentos SET active = :active  WHERE id = :id");

        $stmt->execute([
            ':id' => $id,
            ':active' => $apartment->active == 1 ? '0' : '1'
        ]);

        return $this->findById($id);
    }
}
