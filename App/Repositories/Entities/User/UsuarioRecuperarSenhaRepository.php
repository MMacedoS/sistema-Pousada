<?php

namespace App\Repositories\Entities\User;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\User\UsuarioRecuperarSenha;
use App\Repositories\Contracts\User\IUsuarioRecuperarSenhaRepository;
use App\Repositories\Traits\FindTrait;
use App\Services\EmailService;
use App\Utils\LoggerHelper;

class UsuarioRecuperarSenhaRepository extends SingletonInstance implements IUsuarioRecuperarSenhaRepository {
    const CLASS_NAME = UsuarioRecuperarSenha::class;
    const TABLE = 'usuario_recuperar_senha';
    
    use FindTrait;

    private $emailService;
    private $usuarioRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new UsuarioRecuperarSenha();
        $this->emailService = new EmailService();        
        $this->usuarioRepository = UsuarioRepository::getInstance();
    }

    public function create($user_email)
    {   
        $userExists = $this->usuarioRepository->findByEmail($user_email);

        if(is_null($userExists)) {
            return null;
        }

        $user = $this->model->create([]);

        $user->antiga = $userExists->senha;        
        $user->usuario_id = $userExists->id;       
        $user->ativo = $userExists->ativo;
        
        try {
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . " 
                  set 
                    uuid = :uuid,
                    antiga = :old, 
                    usuario_id = :user_id, 
                    ativo = :active                    
            ");
            $create = $stmt->execute([
                ':uuid' => $user->uuid,
                ':old' => $user->antiga,
                ':user_id' => $user->usuario_id,
                ':active' => $user->ativo ?? 1
            ]);
    
            if (is_null($create)) {
                return null;
            }

            $userFromDb = $this->findByUuid($user->uuid);

            $message = $this->emailService->prepareMessageRecoveryPassword($userExists, $user->uuid);
            $this->emailService->sendEmail(
                $userExists->email,
                'Recuperar Senha',
                $message
            );

            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function updatePassword(array $data, int $user_id) 
    {
        $existingUser = $this->usuarioRepository->findById($user_id);
        if (!$existingUser) {
            return null; 
        }

        $updated = $this->usuarioRepository->update($data, (int)$existingUser->id);

        $this->delete($data['id']);

        return $updated;
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
}