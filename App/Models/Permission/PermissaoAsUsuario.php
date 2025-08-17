<?php

class PermissaoAsUsuario
{

    public $id;
    public $permissao_id;
    public $usuario_id;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): PermissaoAsUsuario {
        $permissao = new PermissaoAsUsuario();
        $permissao->id = $data['id'] ?? null;
        $permissao->permissao_id = $data['permissao_id'] ?? null;
        $permissao->usuario_id = $data['usuario_id'] ?? null;
        $permissao->created_at = $data['created_at'] ?? null;
        $permissao->updated_at = $data['updated_at'] ?? null;
        return $permissao;
    }
}
