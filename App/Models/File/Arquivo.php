<?php

namespace App\Models\File;

use App\Models\Traits\UuidTrait;

class Arquivo {

    use UuidTrait;

    public $id;
    public $uuid;
    public $nome_original;
    public $ext_arquivo;
    public $path;
    public $created_at;
    public $updated_at;

    public function __construct(){}

    public function create(
        array $data
    ): Arquivo {
        $arquivo = new Arquivo();
        $arquivo->id = $data['id'] ?? null;
        $arquivo->uuid = $data['uuid'] ?? $this->generateUUID();
        $arquivo->nome_original = $data['original_name'] ?? null;
        $arquivo->ext_arquivo = $data['ext_archive'] ?? null;
        $arquivo->path = $data['path'] ?? null;
        $arquivo->created_at = $data['created_at'] ?? null;
        $arquivo->updated_at = $data['updated_at'] ?? null;
        return $arquivo;
    }

}