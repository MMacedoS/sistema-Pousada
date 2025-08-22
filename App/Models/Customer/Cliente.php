<?

namespace App\Models\Customer;

use App\Models\Traits\UuidTrait;

class Cliente
{
    use UuidTrait;

    public ?int $id;
    public string $uuid;
    public int $pessoa_fisica_id;
    public ?string $job;
    public ?string $nationality;
    public ?string $doc;
    public ?string $type_doc;
    public ?string $representative;
    public ?string $company;
    public ?string $cnpj_company;
    public ?string $phone_company;
    public ?string $email_company;
    public ?int $active;
    public ?int $is_deleted;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct() {}

    public function create(array $data): Cliente
    {
        $cliente = new Cliente();
        $cliente->id = isset($data['id']) ? $data['id'] : null;
        $cliente->uuid = $data['uuid'] ?? $this->generateUUID();
        $cliente->pessoa_fisica_id = isset($data['pessoa_fisica_id']) ? $data['pessoa_fisica_id'] : null;
        $cliente->job = isset($data['job']) ? $data['job'] : null;
        $cliente->nationality = isset($data['nationality']) ? $data['nationality'] : null;
        $cliente->doc = isset($data['doc']) ? $data['doc'] : null;
        $cliente->type_doc = isset($data['type_doc']) ? $data['type_doc'] : null;
        $cliente->representative = isset($data['representative']) ? $data['representative'] : null;
        $cliente->company = isset($data['company']) ? $data['company'] : null;
        $cliente->cnpj_company = isset($data['cnpj_company']) ? $data['cnpj_company'] : null;
        $cliente->phone_company = isset($data['phone_company']) ? $data['phone_company'] : null;
        $cliente->email_company = isset($data['email_company']) ? $data['email_company'] : null;
        $cliente->active = isset($data['active']) ? $data['active'] : 1;

        return $cliente;
    }

    public function update(array $data, Cliente $cliente): Cliente
    {
        $cliente->id = $data['id'] ?? $cliente->id;
        $cliente->uuid = $data['uuid'] ?? $cliente->uuid;
        $cliente->pessoa_fisica_id = $data['pessoa_fisica_id'] ?? $cliente->pessoa_fisica_id;
        $cliente->job = $data['job'] ?? $cliente->job;
        $cliente->nationality = $data['nationality'] ?? $cliente->nationality;
        $cliente->doc = $data['doc'] ?? $cliente->doc;
        $cliente->type_doc = $data['type_doc'] ?? $cliente->type_doc;
        $cliente->representative = $data['representative'] ?? $cliente->representative;
        $cliente->company = $data['company'] ?? $cliente->company;
        $cliente->cnpj_company = $data['cnpj_company'] ?? $cliente->cnpj_company;
        $cliente->phone_company = $data['phone_company'] ?? $cliente->phone_company;
        $cliente->email_company = $data['email_company'] ?? $cliente->email_company;
        $cliente->active = $data['active'] ?? $cliente->active;

        return $cliente;
    }
}
