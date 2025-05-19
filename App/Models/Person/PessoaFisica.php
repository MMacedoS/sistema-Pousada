<?php

namespace App\Models\Person;

use App\Models\Traits\UuidTrait;

class PessoaFisica {
    
    use UuidTrait;

    public $id;
    public string $uuid;
    public string $nome;
    public ?string $nome_social;
    public string $email;
    public $usuario_id;
    public ?string $endereco;
    public $ativo;
    public ?string $nome_mae;
    public ?string $nome_pai;
    public string $cpf;
    public ?string $rg;
    public ?string $data_nascimento;
    public ?string $telefone;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): PessoaFisica {
        $pessoa_fisica = new PessoaFisica();
        $pessoa_fisica->id = isset($data['id']) ? $data['id'] : null;
        $pessoa_fisica->uuid = $data['uuid'] ?? $this->generateUUID(); // UUID é gerado se não existir
        $pessoa_fisica->nome = isset($data['name']) ? $data['name'] : null;
        $pessoa_fisica->nome_social = isset($data['social_name']) ? $data['social_name'] : null;
        $pessoa_fisica->email = isset($data['email']) ? $data['email'] : null;
        $pessoa_fisica->endereco = isset($data['address']) ? $data['address'] : null;
        $pessoa_fisica->telefone = isset($data['phone']) ? $data['phone'] : null;
        $pessoa_fisica->usuario_id = isset($data['user_id']) ? $data['user_id'] : null;
        $pessoa_fisica->data_nascimento = isset($data['birthday']) ? $data['birthday'] : null;
        $pessoa_fisica->nome_mae = isset($data['mother']) ? $data['mother'] : null;
        $pessoa_fisica->nome_pai = isset($data['father']) ? $data['father'] : null;
        $pessoa_fisica->cpf = isset($data['cpf']) ? $data['cpf'] : null;
        $pessoa_fisica->rg = isset($data['rg']) ? $data['rg'] : null;
        $pessoa_fisica->ativo = isset($data['active']) ? (int)$data['active'] : 1; // Valor padrão 1 se não existir
        $pessoa_fisica->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $pessoa_fisica->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;

        return $pessoa_fisica;
    }

    public function update(array $data, PessoaFisica $pessoaFisica): PessoaFisica
    {
        $pessoaFisica->usuario_id = $data['user_id'] ?? $pessoaFisica->usuario_id;
        $pessoaFisica->nome = $data['name'] ?? $pessoaFisica->nome;
        $pessoaFisica->nome_social = $data['social_name'] ?? $pessoaFisica->nome_social;
        $pessoaFisica->email = $data['email'] ?? $pessoaFisica->email;
        $pessoaFisica->telefone = $data['phone'] ?? $pessoaFisica->telefone;
        $pessoaFisica->cpf = $data['cpf'] ?? $pessoaFisica->cpf;
        $pessoaFisica->nome_mae = $data['mother'] ?? $pessoaFisica->nome_mae;
        $pessoaFisica->nome_pai = $data['father'] ?? $pessoaFisica->nome_pai;
        $pessoaFisica->rg = $data['rg'] ?? $pessoaFisica->rg;
        $pessoaFisica->ativo = $data['active'] ?? $pessoaFisica->ativo;

        return $pessoaFisica;
    }
}