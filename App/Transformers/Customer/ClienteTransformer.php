<?php

namespace App\Transformers\Customer;

use App\Models\Customer\Cliente;
use App\Repositories\Entities\Person\PessoaFisicaRepository;

class ClienteTransformer
{
    public function transform(Cliente $data): array
    {
        return [
            'id' => $data->uuid ?? null,
            'name' => $this->prepareName($data),
            'email' => $this->prepareEmail($data),
            'phone' => $this->preparePhone($data),
            'address' => $this->prepareAddress($data),
            'birthday' => $this->prepareBirthdate($data),
            'pessoa_fisica_id' => $data->pessoa_fisica_id ?? null,
            'nationality' => $data->nationality ?? null,
            'job' => $data->job ?? null,
            'doc' => $data->doc ?? null,
            'type_doc' => $data->type_doc ?? null,
            'representative' => $data->representative ?? null,
            'company' => $data->company ?? null,
            'cnpj_company' => $data->cnpj_company ?? null,
            'phone_company' => $data->phone_company ?? null,
            'email_company' => $data->email_company ?? null,
            'active' => $data->active ?? null,
            'created_at' => $data->created_at ?? null,
            'updated_at' => $data->updated_at ?? null
        ];
    }

    public function transformCollection(array $data): array
    {
        return array_map(fn($item) => $this->transform($item), $data);
    }

    private function prepareName(Cliente $cliente): string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->name;
    }

    private function prepareEmail(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->email ?? null;
    }

    private function preparePhone(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->phone ?? null;
    }

    private function prepareAddress(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->address ?? null;
    }

    private function prepareBirthdate(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->birthday ?? null;
    }
}
