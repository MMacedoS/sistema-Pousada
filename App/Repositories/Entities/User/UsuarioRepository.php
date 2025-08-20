<?php

namespace App\Repositories\Entities\User;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\User\Usuario;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Repositories\Entities\File\ArquivoRepository;
use App\Repositories\Entities\Permission\PermissaoRepository;
use App\Repositories\Entities\Person\PessoaFisicaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class UsuarioRepository extends SingletonInstance implements IUsuarioRepository
{
    private const CLASS_NAME = Usuario::class;
    private const TABLE = 'usuarios';

    use FindTrait;
    private $permissioRepository;
    protected $arquivoRepository;
    protected $pessoaFisicaRepository;

    public function __construct()
    {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();

            // Verificar se a conexão foi estabelecida
            if ($this->conn === null) {
                throw new \Exception("Falha na conexão com o banco de dados no UsuarioRepository - Database getInstance retornou null");
            }

            $this->model = new Usuario();
            $this->permissioRepository = PermissaoRepository::getInstance();
            $this->arquivoRepository = ArquivoRepository::getInstance();
            $this->pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        } catch (\Exception $e) {
            throw new \Exception("Erro no construtor UsuarioRepository: " . $e->getMessage());
        }
    }

    public function all(array $params = [])
    {
        $sql = "SELECT u.uuid as id, u.name, u.email, u.access, u.active, JSON_OBJECT(
                    'id', p.id,
                    'name', p.name,
                    'uuid', p.uuid,
                    'email', p.email,                    
                    'address', p.address,
                    'phone', p.phone
                ) AS pessoa_fisica
              FROM " . self::TABLE . " u inner join pessoa_fisica p on u.id = p.usuario_id ";

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(u.name LIKE :name_email or u.email LIKE :name_email)";
            $bindings[':name_email'] = '%' . $params['name_email'] . '%';
        }

        if (isset($params['access']) && $params['access'] != '') {
            $conditions[] = "u.access = :access";
            $bindings[':access'] = $params['access'];
        }

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "u.active = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        $conditions[] = 'u.is_deleted = :is_deleted';
        $bindings[':is_deleted'] = $params['is_deleted'] ?? 0;

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY u.name DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data, bool $forceNewPassword = true)
    {
        $existingUser = $this->findByEmailAndSector($data['email']);

        if (!is_null($existingUser)) {
            return $existingUser;
        }

        $user = $this->model->create(
            $data,
            $forceNewPassword
        );
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn
                ->prepare(
                    "INSERT INTO " . self::TABLE . " 
                  set 
                    uuid = :uuid,
                    name = :name, 
                    email = :email, 
                    access = :access,
                    password = :password
            "
                );
            $create = $stmt->execute([
                ':uuid' => $user->uuid,
                ':name' => $user->name,
                ':access' => $user->access,
                ':email' => $user->email,
                ':password' => $user->password
            ]);

            if (is_null($create)) {
                $this->conn->rollBack();
                return null;
            }

            $userFromDb = $this->findByUuid($user->uuid);

            $this->assignPermissionsToUser($userFromDb);

            $data['usuario_id'] = $userFromDb->id;

            $person = $this->pessoaFisicaRepository->create($data);

            if (is_null($person)) {
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();
            return $userFromDb;
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM " . self::TABLE . " WHERE email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, self::CLASS_NAME);

            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function findByIdWithPhoto(int $id)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT u.name, u.email, u.access, u.active, u.arquivo_id, u.id as code, u.uuid as id, a.path as photo  FROM "
                    . self::TABLE .
                    " u LEFT JOIN arquivos a on a.id = u.arquivo_id 
                WHERE u.id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, self::CLASS_NAME);

            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function findByEmailAndSector(string $email)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM " . self::TABLE . " WHERE email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, self::CLASS_NAME);

            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null;
        }

        $data['existing_password'] = $existingUser->password;
        isset($data['password']) ? $password = (string)$data['password'] : $password = $existingUser->password;
        $user = $this->model
            ->update(
                $data,
                $existingUser,
                !hash_equals(
                    $password,
                    $existingUser->password
                )
            );

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET 
                        name = :name, 
                        email = :email, 
                        access = :access,
                        active = :status,
                        password = :senha
                    WHERE id = :id"
            );

            $parameters = [
                ':id' => $id,
                ':name' => $user->name,
                ':email' => $user->email,
                ':access' => $user->access,
                ':status' => $user->active,
                ':senha' => $user->password
            ];

            $updated = $stmt->execute($parameters);

            if (!$updated) {
                return null;
            }

            $userFromDb = $this->findByIdWithPhoto($id);

            $this->assignPermissionsToUser($userFromDb);

            $this->pessoaFisicaRepository->updateByUser($data, $userFromDb->code);

            if (isset($userFromDb->password)) {
                $userFromDb->id = $userFromDb->uuid;
                unset($userFromDb->password);
                unset($userFromDb->uuid);
            }

            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage() . $th->getFile() . $th->getLine());
            return null;
        }
    }

    public function updatePassword(array $data, int $id)
    {
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null;
        }

        if (!password_verify($data['password_old'], $existingUser->password)) {
            return null;
        }

        return $this->update($data, (int)$existingUser->id);
    }

    public function getLogin(string $email, string $senha)
    {
        if (empty($email) || empty($senha)) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare(
                "SELECT id as code, password, name, email, access, active, arquivo_id, uuid as id 
                    FROM " . self::TABLE . " 
                    WHERE email = :email 
                    and is_deleted = 0
                    and active = 1"
            );
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $user = $stmt->fetch();

            if (!$user) {
                return null;
            }

            if (!password_verify($senha, $user->password)) {
                return null;
            }

            unset($user->uuid, $user->password);

            return $user;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function delete(int $id)
    {
        $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . " 
             SET is_deleted = 1 
             WHERE id = :id"
            );

        $updated = $stmt->execute([':id' => $id]);

        if ($updated) {
            $this->pessoaFisicaRepository->deleteByUserId($id);
        }

        return $updated;
    }

    public function active($id): ?bool
    {
        $usuario = $this->findById((int)$id);

        if (is_null($usuario)) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare("UPDATE " . self::TABLE . " SET active=:active WHERE id = :id");
            $actived = $stmt->execute([
                ':active' => 1,
                ':id' => $id
            ]);
            if ($actived) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function remove($id): ?bool
    {
        $usuario = $this->findById((int)$id);

        if (is_null($usuario)) {
            return null;
        }

        try {
            if (!$this->removePermissions($id)) {
                return null;
            };

            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if ($delete) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function findPermissions(int $usuario_id)
    {
        $stmt = $this->conn
            ->prepare(
                "SELECT permissao_as_usuario.* 
                FROM permissao_as_usuario 
                where usuario_id = :usuario_id"
            );
        $stmt->bindValue(':usuario_id', $usuario_id);
        $stmt->execute();
        $user_permissions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $user_permissions;
    }

    public function addPermissions(array $data, int $id): bool
    {
        if (empty($data['permissions']) || $id <= 0) {
            return false;
        }

        if (!$this->removePermissions($id)) {
            return false;
        }

        foreach ($data['permissions'] as $permission) {
            $stmt = $this->conn->prepare(
                "INSERT INTO permissao_as_usuario (permissao_id, usuario_id) 
                VALUES (:permissao_id, :usuario_id)"
            );

            $success = $stmt->execute([
                ':permissao_id' => (int)$permission,
                ':usuario_id' => (int)$id
            ]);

            if (!$success) {
                return false;
            }
        }

        return true;
    }

    public function removePermissions(int $usuario_id): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM permissao_as_usuario WHERE usuario_id = :usuario_id"
        );
        $deleted = $stmt->execute([':usuario_id' => (int)$usuario_id]);

        return $deleted;
    }

    private function assignPermissionsToUser(Usuario $userFromDb)
    {
        $access = !is_null($userFromDb->access) ? $userFromDb->access : null;

        $permissions = $this->permissionList($access);

        if (is_null($permissions)) {
            return $userFromDb;
        }

        $permissionNames = array_map(fn($permission) => $permission['name'], $permissions);

        $placeholders = implode(',', array_fill(0, count($permissionNames), '?'));

        $stmt = $this->conn->prepare(
            "SELECT id FROM permissao WHERE name IN ($placeholders)"
        );

        $stmt->execute($permissionNames);

        $permissionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->addPermissions(['permissions' => $permissionIds], $userFromDb->code ?? $userFromDb->id);

        return $userFromDb;
    }

    public function updatePhoto($file, $dir, $id_user)
    {
        $file = $this->arquivoRepository->create($file, $dir);

        $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . " 
             SET arquivo_id = :file_id 
             WHERE id = :id"
            );

        $updated = $stmt->execute(
            [
                ':id' => $id_user,
                ':file_id' => $file->id
            ]
        );

        return $this->findByIdWithPhoto($id_user);
    }

    private function permissionList($role)
    {
        if ($role == 'administrador') {
            return [
                ['name' => 'users.view'],
                ['name' => 'users.create'],
                ['name' => 'users.edit'],
                ['name' => 'users.delete'],
                ['name' => 'reservations.view'],
                ['name' => 'reservations.create'],
                ['name' => 'reservations.edit'],
                ['name' => 'reservations.cancel'],
                ['name' => 'reservations.checkin'],
                ['name' => 'reservations.checkout'],
                ['name' => 'apartments.view'],
                ['name' => 'apartments.create'],
                ['name' => 'apartments.edit'],
                ['name' => 'apartments.delete'],
                ['name' => 'apartments.status'],
                ['name' => 'customers.view'],
                ['name' => 'customers.create'],
                ['name' => 'customers.edit'],
                ['name' => 'customers.delete'],
                ['name' => 'cashbox.view'],
                ['name' => 'cashbox.open'],
                ['name' => 'cashbox.close'],
                ['name' => 'cashbox.transactions'],
                ['name' => 'financial.reports'],
                ['name' => 'sales.view'],
                ['name' => 'sales.create'],
                ['name' => 'sales.cancel'],
                ['name' => 'bar.sales'],
                ['name' => 'bar.inventory'],
                ['name' => 'products.view'],
                ['name' => 'products.create'],
                ['name' => 'products.edit'],
                ['name' => 'products.delete'],
                ['name' => 'reports.reservations'],
                ['name' => 'reports.financial'],
                ['name' => 'reports.occupancy'],
                ['name' => 'reports.customers'],
                ['name' => 'settings.view'],
                ['name' => 'settings.edit'],
                ['name' => 'permissions.manage'],
                ['name' => 'dashboard.admin'],
                ['name' => 'dashboard.manager'],
                ['name' => 'dashboard.reception'],
                ['name' => 'employees.view'],
                ['name' => 'employees.create'],
                ['name' => 'employees.edit'],
                ['name' => 'employees.delete'],
                ['name' => 'employees.status'],
            ];
        }

        if ($role == 'gerente') {
            // Gestão operacional e supervisão
            return [
                ['name' => 'users.view'],
                ['name' => 'users.create'],
                ['name' => 'users.edit'],
                ['name' => 'reservations.view'],
                ['name' => 'reservations.create'],
                ['name' => 'reservations.edit'],
                ['name' => 'reservations.cancel'],
                ['name' => 'reservations.checkin'],
                ['name' => 'reservations.checkout'],
                ['name' => 'apartments.view'],
                ['name' => 'apartments.create'],
                ['name' => 'apartments.edit'],
                ['name' => 'apartments.status'],
                ['name' => 'customers.view'],
                ['name' => 'customers.create'],
                ['name' => 'customers.edit'],
                ['name' => 'cashbox.view'],
                ['name' => 'cashbox.open'],
                ['name' => 'cashbox.close'],
                ['name' => 'financial.reports'],
                ['name' => 'sales.view'],
                ['name' => 'sales.create'],
                ['name' => 'sales.cancel'],
                ['name' => 'products.view'],
                ['name' => 'products.create'],
                ['name' => 'products.edit'],
                ['name' => 'reports.reservations'],
                ['name' => 'reports.financial'],
                ['name' => 'reports.occupancy'],
                ['name' => 'reports.customers'],
                ['name' => 'settings.view'],
                ['name' => 'dashboard.admin'],
                ['name' => 'dashboard.manager'],
                ['name' => 'employees.view'],
                ['name' => 'employees.create'],
                ['name' => 'employees.edit'],
                ['name' => 'employees.delete'],
                ['name' => 'employees.status'],
            ];
        }

        if ($role == 'recepcionista') {
            // Atendimento ao cliente e registro de reservas
            return [
                ['name' => 'reservations.view'],
                ['name' => 'reservations.create'],
                ['name' => 'reservations.edit'],
                ['name' => 'reservations.checkin'],
                ['name' => 'reservations.checkout'],
                ['name' => 'apartments.view'],
                ['name' => 'apartments.status'],
                ['name' => 'customers.view'],
                ['name' => 'customers.create'],
                ['name' => 'customers.edit'],
                ['name' => 'sales.view'],
                ['name' => 'sales.create'],
                ['name' => 'reports.reservations'],
                ['name' => 'reports.occupancy'],
                ['name' => 'dashboard.reception'],
            ];
        }

        if ($role == 'caixa') {
            // Operações de caixa e financeiro
            return [
                ['name' => 'reservations.view'],
                ['name' => 'reservations.checkout'],
                ['name' => 'customers.view'],
                ['name' => 'cashbox.view'],
                ['name' => 'cashbox.open'],
                ['name' => 'cashbox.close'],
                ['name' => 'cashbox.transactions'],
                ['name' => 'sales.view'],
                ['name' => 'sales.create'],
                ['name' => 'sales.cancel'],
                ['name' => 'products.view'],
                ['name' => 'reports.financial'],
                ['name' => 'dashboard.reception'],
            ];
        }

        if ($role == 'bar' || $role == 'recepcionista_bar') {
            // Acesso restrito ao controle do bar
            return [
                ['name' => 'customers.view'],
                ['name' => 'sales.view'],
                ['name' => 'sales.create'],
                ['name' => 'sales.cancel'],
                ['name' => 'bar.sales'],
                ['name' => 'bar.inventory'],
                ['name' => 'products.view'],
                ['name' => 'cashbox.view'],
                ['name' => 'cashbox.transactions'],
                ['name' => 'dashboard.reception'],
            ];
        }

        // Caso não se enquadre em nenhum perfil conhecido
        return [];
    }
}
