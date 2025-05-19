<?php

namespace App\Models\Permission;

use App\Models\Traits\UuidTrait;

class Permissao {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $name;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Permissao {
        $permissao = new Permissao();
        $permissao->id = $data['id'] ?? null;
        $permissao->uuid = $data['uuid'] ?? $this->generateUUID();
        $permissao->name = $data['name'];
        $permissao->description = $data['description']; 
        $permissao->created_at = $data['created_at'] ?? null;
        $permissao->updated_at = $data['updated_at'] ?? null;
        return $permissao;
    }
}