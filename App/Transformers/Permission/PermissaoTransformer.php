<?php

namespace App\Transformers\Permission;

use App\Models\Permission\Permissao;

class PermissaoTransformer
{
    public function transform(Permissao $permissao)
    {
        return [
            'id' => $permissao->id,
            'name' => $permissao->name,
            'description' => $permissao->description,
            'created_at' => $permissao->created_at,
            'updated_at' => $permissao->updated_at,
        ];
    }

    public function transformCollection(array $permissoes)
    {
        return array_map(function ($permissao) {
            return $this->transform($permissao);
        }, $permissoes);
    }
}
