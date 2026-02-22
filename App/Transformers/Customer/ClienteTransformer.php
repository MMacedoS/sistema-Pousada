<?php

namespace App\Transformers\Customer;

use App\Models\Customer\Cliente;
use App\Repositories\Entities\Person\PessoaFisicaRepository;

class ClienteTransformer
{
    public function transform(Cliente $data): array
    {
        $addressData = $this->parseAddress($data);

        return [
            'id' => $data->uuid ?? null,
            'code' => $data->id ?? null,
            'name' => $this->prepareName($data),
            'email' => $this->prepareEmail($data),
            'phone' => $this->preparePhone($data),
            'cpf' => $this->prepareDoc($data),
            'street' => $addressData['street'],
            'number' => $addressData['number'],
            'neighborhood' => $addressData['neighborhood'],
            'city' => $addressData['city'],
            'state' => $addressData['state'],
            'zipCode' => $addressData['zipCode'],
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

    private function prepareDoc(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->doc ?? null;
    }

    private function parseAddress(Cliente $cliente): array
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);

        // Se os campos separados já existem no banco, usa eles diretamente
        return [
            'street' => $person->street ?? null,
            'number' => $person->number ?? null,
            'neighborhood' => $person->neighborhood ?? null,
            'city' => $person->city ?? null,
            'state' => $person->state ?? null,
            'zipCode' => $person->zipCode ?? null,
        ];
    }

    private function prepareBirthdate(Cliente $cliente): ?string
    {
        $pessoaFisicaRepository = PessoaFisicaRepository::getInstance();
        $person = $pessoaFisicaRepository->findById($cliente->pessoa_fisica_id);
        return $person->birthday ?? null;
    }
}
