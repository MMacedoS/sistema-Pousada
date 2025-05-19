<?php
 namespace App\Repositories\Traits;
 
trait FindTrait{
    public function findById(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . self::TABLE . " where id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  
        if (is_null($register)) {
            return null;
        }
    
        return $register;
    }

    public function findByUuid(string $uuid)
    {
        if (is_null($uuid)) {
            return null;
        }
    
        $stmt = $this->conn->prepare("SELECT * FROM " . self::TABLE . " WHERE uuid = :uuid");
        $stmt->bindValue(':uuid', $uuid, \PDO::PARAM_STR);
        $stmt->execute();
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch(); 
        if (!$register) {
            return null;
        }
    
        return $register;
    }
    
}