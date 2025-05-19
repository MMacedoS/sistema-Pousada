<?php

namespace App\Repositories\Entities\User;

use App\Config\Database;
use App\Models\User\Usuario;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Repositories\Entities\File\ArquivoRepository;
use App\Repositories\Entities\Permission\PermissaoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class UsuarioRepository implements IUsuarioRepository {
    const CLASS_NAME = Usuario::class;
    const TABLE = 'usuarios';
    
    use FindTrait;
    protected $conn;
    protected $model;
    private $permissioRepository;
    protected $arquivoRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Usuario();
        $this->permissioRepository = new PermissaoRepository();
        $this->arquivoRepository = new ArquivoRepository();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(nome LIKE :name_email or email LIKE :name_email)";
            $bindings[':name_email'] = '%' . $params['name_email'] . '%';
        }

        if (isset($params['access']) && $params['access'] != '') {
            $conditions[] = "acesso = :access";
            $bindings[':access'] = $params['access'];
        }

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "ativo = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome DESC";

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

        try {
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . " 
                  set 
                    uuid = :uuid,
                    nome = :name, 
                    email = :email, 
                    senha = :password,
                    acesso = :sector
            ");
            $create = $stmt->execute([
                ':uuid' => $user->uuid,
                ':name' => $user->nome,
                ':email' => $user->email,
                ':password' => $user->senha,
                ':sector' => $user->acesso
            ]);
    
            if (is_null($create)) {
                return null;
            }

            $userFromDb = $this->findByUuid($user->uuid);
            $this->assignPermissionsToUser($userFromDb);            
            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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
        } finally {          
            Database::getInstance()->closeConnection();
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
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null; 
        }

        $data['existing_password'] = $existingUser->senha;
        isset($data['password']) ? $senha = (string)$data['password'] : $senha = $existingUser->senha;
        $user = $this->model
            ->update(
                $data, 
                $existingUser, 
                !hash_equals(
                    $senha, 
                    $existingUser->senha
                )
            );

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET 
                        nome = :name, 
                        email = :email, 
                        ativo = :status,
                        acesso = :sector,
                        senha = :senha
                    WHERE id = :id"
            );

            $parameters = [
                ':id' => $id,
                ':name' => $user->nome,
                ':email' => $user->email,
                ':sector' => $user->acesso,
                ':status' => $user->ativo,
                ':senha' => $user->senha
            ];

            $updated = $stmt->execute($parameters);

            if (!$updated) {
                return null;
            }
            
            $userFromDb = $this->findById($id);

            $this->assignPermissionsToUser($userFromDb);

            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function updatePassword(array $data, int $id) 
    {
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null; 
        }

        if (!password_verify($data['password_old'], $existingUser->senha)) {
            return null;
        }

        return $this->update($data, (int)$existingUser->id);
    }

    public function getLogin(string $email, string $senha)
    {
        if (empty($email) || empty($senha)) {
            return null;
        }
    
        $stmt = $this->conn->prepare(
            "SELECT id as code, senha, nome, email, acesso, ativo, arquivo_id, uuid as id 
             FROM " . self::TABLE . " 
             WHERE email = :email"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
    
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $user = $stmt->fetch();
    
        if (!$user) {
            return null;
        }
    
        if (!password_verify($senha, $user->senha)) {
            return null;
        }
    
        unset($user->uuid, $user->senha);
    
        return $user;
    }    

    public function delete(int $id) 
    {
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . " 
             SET ativo = 0 
             WHERE id = :id"
        );

        $updated = $stmt->execute([':id' => $id]);

        return $updated;
    }

    public function remove($id) :?bool 
    {
        
        $usuario = $this->findById((int)$id);
        
        if (is_null($usuario)) {
            return null;
        }
        
        if(!$this->removePermissions($id)) {
            return null;
        };
        
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
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function findPermissions(int $usuario_id) 
    {
        $stmt = $this->conn
            ->prepare(
                "SELECT permissao_as_usuario.* 
                FROM permissao_as_usuario 
                where usuario_id = :usuario_id");
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
        $access = $userFromDb->acesso !== 'administrativo' ? $userFromDb->acesso : null;
        
        $permissions = $this->permissionList($access);

        if (is_null($permissions)) {
            return $userFromDb;
        }

        $permissionIds = array_map(fn($permission) => $permission['id'], $permissions);
        
        $this->addPermissions(['permissions' => $permissionIds], $userFromDb->id);

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

        $updated = $stmt->execute([':id' => $id_user, ':file_id' => $file->id]);

        return $file;
    }

    private function permissionList ($sector) 
    {
        if ($sector == 'administrativo') {
            $permissao = array(
                array('id' => '1', 'name' => 'visualizar usuários','description' => 'Visualizar todos os usuarios'),
                array('id' => '2', 'name' => 'editar usuarios','description' => 'atualizar dados do usuários'),
                array('id' => '3', 'name' => 'criar usuários','description' => 'cadastrar usuários'),
                array('id' => '4', 'name' => 'deletar usuários','description' => 'excluir conta de usuários'),
                array('id' => '5', 'name' => 'visualizar cadastro','description' => 'acesso ao cadastros '),
                array('id' => '6', 'name' => 'visualizar turmas','description' => 'visualizar turmas geral'),
                array('id' => '7', 'name' => 'visualizar professores','description' => 'visualizar professores'),
                array('id' => '8', 'name' => 'visualizar estudantes','description' => 'visualizar estudantes'),
                array('id' => '9', 'name' => 'visualizar disciplinas','description' => 'visualizar disciplinas'),
                array('id' => '10', 'name' => 'visualizar planos','description' => 'visualizar planos'),
                array('id' => '11', 'name' => 'visualizar turmas estudantes','description' => 'visualizar turma estudantes'),
                array('id' => '12', 'name' => 'visualizar mensalidades','description' => 'visualizar mensalidades'),
                array('id' => '13', 'name' => 'deletar professores','description' => 'deletar professore'),
                array('id' => '14', 'name' => 'deletar estudantes','description' => 'deletar apartamento'),
                array('id' => '15', 'name' => 'editar professores','description' => 'editar apartamento'),
                array('id' => '16', 'name' => 'editar estudantes','description' => 'editar cliente'),
                array('id' => '17', 'name' => 'cadastrar professores','description' => 'cadastrar cliente'),
                array('id' => '18', 'name' => 'cadastrar estudantes','description' => 'cadastrar apartamento'),
                array('id' => '19', 'name' => 'cadastrar turmas','description' => 'cadastrar dados de reservas'),
                array('id' => '20', 'name' => 'editar turmas','description' => 'editar dados de reservas'),
                array('id' => '21', 'name' => 'deletar turmas','description' => 'deleção de dados das reservas'),
                array('id' => '22', 'name' => 'editar disciplinas','description' => 'editar produto'),
                array('id' => '23', 'name' => 'visualizar conteudos','description' => 'visualizar produtos'),
                array('id' => '24', 'name' => 'deletar conteudos','description' => 'deletar produtos'),
                array('id' => '25', 'name' => 'cadastrar conteudos','description' => 'cadastrar produtos'),
                array('id' => '26', 'name' => 'cadastrar planos','description' => 'cadastrar vendas'),
                array('id' => '27', 'name' => 'visualizar pedagogico','description' => 'visualizar as ações para menu pedagogicos'),
                array('id' => '28', 'name' => 'visualizar financeiro','description' => 'visualizar ações do bloco financeiro'),
                array('id' => '29', 'name' => 'editar planos','description' => 'editar os planos'),
                array('id' => '30', 'name' => 'deletar planos','description' => 'deletar planos de mensalidade'),
                array('id' => '31', 'name' => 'visualizar contas bancarias','description' => 'acesso visualizar contas bancarias '),
                array('id' => '32', 'name' => 'cadastrar contas','description' => ''),
                array('id' => '33', 'name' => 'editar contas','description' => ''),
                array('id' => '34', 'name' => 'deletar contas','description' => ''),
                array('id' => '37', 'name' => 'visualizar turma e estudante','description' => 'visualizar turma e estudante'),
                array('id' => '38', 'name' => 'vincular turmas e estudantes','description' => 'vincular turmas e estudantes'),
                array('id' => '39', 'name' => 'editar turmas e estudantes','description' => 'editar turmas e estudantes'),
                array('id' => '40', 'name' => 'cadastrar turmas e estudantes','description' => 'cadastrar turmas e estudantes'),
                array('id' => '41', 'name' => 'inativar vinculos','description' => 'inativar vinculos'),
                array('id' => '79', 'name' => 'cadastrar mensalidades','description' => 'cadastrar mensalidades'),
                array('id' => '123', 'name' => 'editar mensalidade','description' => 'edição dos dados da mensalidade'),
                array('id' => '124', 'name' => 'cancelar mensalidades','description' => 'alterar a situação da mensalidade para cancelado'),
                array('id' => '125', 'name' => 'efetivar mensalidade','description' => 'alterar o status da mensalidade para pago'),
                array('id' => '126', 'name' => 'visualizar cards dashboard','description' => 'visualizar cards dashboard'),
                array('id' => '127', 'name' => 'editar turmas-disciplinas','description' => 'editar turmas-disciplinas'),
                array('id' => '128', 'name' => 'deletar turmas-disciplinas','description' => 'deletar turmas-disciplinas'),
                array('id' => '129', 'name' => 'vincular turmas-disciplinas','description' => 'vincular turmas-disciplinas'),
                array('id' => '130', 'name' => 'visualizar atividades','description' => 'visualizar atividades'),
                array('id' => '131', 'name' => 'cadastrar atividades','description' => 'cadastrar atividades'),
                array('id' => '132', 'name' => 'cadastrar coordenadores','description' => 'cadastrar coordenadores'),
                array('id' => '133', 'name' => 'editar coordenadores','description' => 'editar coordenadores'),
                array('id' => '134', 'name' => 'visualizar coordenadores','description' => 'visualizar coordenadores'),
                array('id' => '135', 'name' => 'deletar coordenadores','description' => 'deletar coordenadores'),
                array('id' => '136', 'name' => 'visualizar bimestres','description' => 'visualizar bimestres'),
                array('id' => '137', 'name' => 'cadastrar pessoa','description' => 'cadastrar pessoa'),
                array('id' => '138', 'name' => 'editar pessoa','description' => 'editar pessoa'),
                array('id' => '139', 'name' => 'deletar pessoa','description' => 'deletar pessoa'),
                array('id' => '140', 'name' => 'visualizar pessoas','description' => 'visualizar pessoas'),
                array('id' => '142', 'name' => 'visualizar carga_horaria','description' => 'visualizar cargas horarias'),
                array('id' => '143', 'name' => 'visualizar periodos','description' => 'visualizar periodos'),
                array('id' => '144', 'name' => 'editar periodo','description' => 'editar periodos'),
                array('id' => '145', 'name' => 'cadastrar periodo','description' => 'cadastrar periodo'),
                array('id' => '146', 'name' => 'deletar periodo','description' => 'deletar periodo'),
              );
            return $permissao;
        }

        if ($sector == 'coordenador') {
            $permissao = array(
                array('id' => '5', 'name' => 'visualizar cadastro','description' => 'acesso ao cadastros '),
                array('id' => '6', 'name' => 'visualizar turmas','description' => 'visualizar turmas geral'),
                array('id' => '7', 'name' => 'visualizar professores','description' => 'visualizar professores'),
                array('id' => '8', 'name' => 'visualizar estudantes','description' => 'visualizar estudantes'),
                array('id' => '9', 'name' => 'visualizar disciplinas','description' => 'visualizar disciplinas'),
                array('id' => '10', 'name' => 'visualizar planos','description' => 'visualizar planos'),
                array('id' => '11', 'name' => 'visualizar turmas estudantes','description' => 'visualizar turma estudantes'),
                array('id' => '12', 'name' => 'visualizar mensalidades','description' => 'visualizar mensalidades'),
                array('id' => '13', 'name' => 'deletar professores','description' => 'deletar professore'),
                array('id' => '14', 'name' => 'deletar estudantes','description' => 'deletar apartamento'),
                array('id' => '15', 'name' => 'editar professores','description' => 'editar apartamento'),
                array('id' => '16', 'name' => 'editar estudantes','description' => 'editar cliente'),
                array('id' => '17', 'name' => 'cadastrar professores','description' => 'cadastrar cliente'),
                array('id' => '18', 'name' => 'cadastrar estudantes','description' => 'cadastrar apartamento'),
                array('id' => '19', 'name' => 'cadastrar turmas','description' => 'cadastrar dados de reservas'),
                array('id' => '20', 'name' => 'editar turmas','description' => 'editar dados de reservas'),
                array('id' => '21', 'name' => 'deletar turmas','description' => 'deleção de dados das reservas'),
                array('id' => '22', 'name' => 'editar disciplinas','description' => 'editar produto'),
                array('id' => '23', 'name' => 'visualizar conteudos','description' => 'visualizar produtos'),
                array('id' => '24', 'name' => 'deletar conteudos','description' => 'deletar produtos'),
                array('id' => '25', 'name' => 'cadastrar conteudos','description' => 'cadastrar produtos'),
                array('id' => '26', 'name' => 'cadastrar planos','description' => 'cadastrar vendas'),
                array('id' => '27', 'name' => 'visualizar pedagogico','description' => 'visualizar as ações para menu pedagogicos'),
                array('id' => '29', 'name' => 'editar planos','description' => 'editar os planos'),
                array('id' => '37', 'name' => 'visualizar turma e estudante','description' => 'visualizar turma e estudante'),
                array('id' => '38', 'name' => 'vincular turmas e estudantes','description' => 'vincular turmas e estudantes'),
                array('id' => '39', 'name' => 'editar turmas e estudantes','description' => 'editar turmas e estudantes'),
                array('id' => '40', 'name' => 'cadastrar turmas e estudantes','description' => 'cadastrar turmas e estudantes'),
                array('id' => '41', 'name' => 'inativar vinculos','description' => 'inativar vinculos'),
                array('id' => '126', 'name' => 'visualizar cards dashboard','description' => 'visualizar cards dashboard'),
                array('id' => '127', 'name' => 'editar turmas-disciplinas','description' => 'editar turmas-disciplinas'),
                array('id' => '128', 'name' => 'deletar turmas-disciplinas','description' => 'deletar turmas-disciplinas'),
                array('id' => '129', 'name' => 'vincular turmas-disciplinas','description' => 'vincular turmas-disciplinas'),
                array('id' => '130', 'name' => 'visualizar atividades','description' => 'visualizar atividades'),
                array('id' => '131', 'name' => 'cadastrar atividades','description' => 'cadastrar atividades'),
                array('id' => '132', 'name' => 'cadastrar coordenadores','description' => 'cadastrar coordenadores'),
                array('id' => '133', 'name' => 'editar coordenadores','description' => 'editar coordenadores'),
                array('id' => '134', 'name' => 'visualizar coordenadores','description' => 'visualizar coordenadores'),
                array('id' => '135', 'name' => 'deletar coordenadores','description' => 'deletar coordenadores'),
                array('id' => '136', 'name' => 'visualizar bimestres','description' => 'visualizar bimestres'),
                array('id' => '137', 'name' => 'cadastrar pessoa','description' => 'cadastrar pessoa'),
                array('id' => '138', 'name' => 'editar pessoa','description' => 'editar pessoa'),
                array('id' => '139', 'name' => 'deletar pessoa','description' => 'deletar pessoa'),
                array('id' => '140', 'name' => 'visualizar pessoas','description' => 'visualizar pessoas'),
                array('id' => '142', 'name' => 'visualizar carga_horaria','description' => 'visualizar cargas horarias'),
                array('id' => '143', 'name' => 'visualizar periodos','description' => 'visualizar periodos'),
                array('id' => '144', 'name' => 'editar periodo','description' => 'editar periodos'),
                array('id' => '145', 'name' => 'cadastrar periodo','description' => 'cadastrar periodo'),
                array('id' => '146', 'name' => 'deletar periodo','description' => 'deletar periodo')
              );
            return $permissao;
        }

        if ($sector == 'professor') {
            $permissao = array(                
                array('id' => '36', 'name' => 'estudante','description' => 'permissao para acesso estudante')
              );
            return $permissao;
        }
        
        if ($sector == 'estudante') {
            $permissao = array(                
                array('id' => '141', 'name' => 'responsavel_legal','description' => 'responsavel legal dos estudantes')
              );
            return $permissao;
        }

        if ($sector == 'responsavel_legal') {
            $permissao = array(                
                array('id' => '141', 'name' => 'responsavel_legal','description' => 'responsavel legal dos estudantes')
              );
            return $permissao;
        }
    }
}