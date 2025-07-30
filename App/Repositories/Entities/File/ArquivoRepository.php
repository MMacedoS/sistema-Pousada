<?php

namespace App\Repositories\Entities\File;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\File\Arquivo;
use App\Repositories\Contracts\File\IArquivoRepository;
use App\Repositories\Traits\FindTrait;

class ArquivoRepository extends SingletonInstance implements IArquivoRepository 
{
    private const CLASS_NAME = Arquivo::class;
    private const TABLE = 'arquivos';

    use FindTrait;

    public function __construct(){
        $this->model = new Arquivo();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function allFiles(array $params = []){
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['original_name'])) {
            $conditions[] = "nome_original = :nome_original";
            $bindings[':nome_original'] = $params['original_name'];
        }

        if (isset($params['ext_archive'])) {
            $conditions[] = "ext_arquivo = :ext_arquivo";
            $bindings[':ext_arquivo'] = $params['ext_archive'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);  
    }

    public function create(array $data, string $dir){
        $file = $this->model->create($data);
        
        $manipulation = publicPath($data['file'], $dir);

        if(is_null($manipulation)) {
            return null;
        }
        
        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome_original = :original_name,
                        ext_arquivo = :ext_archive,
                        path = :archive
                "
            );

            $create = $stmt->execute([
                ':uuid' => $file->uuid,
                ':original_name' => $manipulation['new_name'],
                ':ext_archive' => $manipulation['ext'],
                ':archive' => $manipulation['path']
            ]);

            if(is_null($create)){
                return null;
            }

            $archiveFromDb = $this->findByUuid($file->uuid);
            return $archiveFromDb;
        } catch(\Throwable $th){
            return null;
        } finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function createFilePDF($file, string $dir)
    {
        $data = uploadFile($dir, $file);
    
        $manipulation = $this->model->create($data);

        if(is_null($file)) {
            return null;
        }
        
        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome_original = :original_name,
                        ext_arquivo = :ext_archive,
                        path = :archive
                "
            );

            $create = $stmt->execute([
                ':uuid' => $manipulation->uuid,
                ':original_name' => $manipulation->nome_original,
                ':ext_archive' => $manipulation->ext_arquivo,
                ':archive' => $manipulation->path
            ]);

            if(is_null($create)){
                return null;
            }

            $archiveFromDb = $this->findByUuid($manipulation->uuid);
            return $archiveFromDb;
        } catch(\Throwable $th){
            return null;
        } finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, string $dir, int $id){
        $file = $this->model->create($data);

        $manipulation = publicPath($data['file'], $dir);

        if(!$manipulation){
            return null;
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                    SET
                        nome_original = :original_name,
                        ext_arquivo = :ext_archive,
                        arquivo = :archive
                    WHERE id = :id
                " 
            );

            $updated = $stmt->execute([
                ':original_name' => $manipulation['name'],
                ':ext_archive' => $manipulation['ext'],
                ':archive' => $manipulation['new_name'],
                ':id' => $id
            ]);

            if(!$updated){
                return null;
            }

            return $this->findByUuid($file->uuid);
        } catch(\Throwable $th){
            return null;
        } finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "DELETE FROM " . self::TABLE . " WHERE id = :id"
        );

        $deleted = $stmt->execute(['id' => $id]);

        return $deleted;
    }
}