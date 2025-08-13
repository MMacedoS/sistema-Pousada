# Sistema de Permissões - API REST

## Visão Geral

Este sistema implementa um controle granular de permissões para todas as operações da API REST do sistema de gestão hoteleira.

## Como Funciona

### 1. Autenticação

- O usuário faz login via `/api/v1/login`
- Recebe um token JWT e suas permissões na resposta
- O token deve ser enviado no header `Authorization: Bearer {token}` para todas as requisições

### 2. Verificação de Permissões

- Cada endpoint da API verifica se o usuário tem a permissão necessária
- Se não tiver permissão, retorna erro `403 Forbidden`
- Se o token for inválido, retorna erro `401 Unauthorized`

## Permissões por Módulo

### Usuários

- `users.view` - Visualizar lista de usuários
- `users.create` - Criar novos usuários
- `users.edit` - Editar dados de usuários
- `users.delete` - Excluir usuários

### Reservas

- `reservations.view` - Visualizar reservas
- `reservations.create` - Criar novas reservas
- `reservations.edit` - Editar reservas existentes
- `reservations.cancel` - Cancelar reservas
- `reservations.checkin` - Realizar check-in
- `reservations.checkout` - Realizar check-out

### Apartamentos/Quartos

- `apartments.view` - Visualizar apartamentos
- `apartments.create` - Cadastrar apartamentos
- `apartments.edit` - Editar apartamentos
- `apartments.delete` - Excluir apartamentos
- `apartments.status` - Alterar status apartamentos

### Clientes

- `customers.view` - Visualizar clientes
- `customers.create` - Cadastrar clientes
- `customers.edit` - Editar dados de clientes
- `customers.delete` - Excluir clientes

### Caixa

- `cashbox.view` - Visualizar movimentações caixa
- `cashbox.open` - Abrir caixa
- `cashbox.close` - Fechar caixa
- `cashbox.transactions` - Realizar transações

### Financeiro

- `financial.reports` - Acessar relatórios financeiros

### Vendas

- `sales.view` - Visualizar vendas
- `sales.create` - Realizar vendas
- `sales.cancel` - Cancelar vendas

### Bar

- `bar.sales` - Vendas específicas do bar
- `bar.inventory` - Controle estoque do bar

### Produtos

- `products.view` - Visualizar produtos
- `products.create` - Cadastrar produtos
- `products.edit` - Editar produtos
- `products.delete` - Excluir produtos

### Relatórios

- `reports.reservations` - Relatórios de reservas
- `reports.financial` - Relatórios financeiros
- `reports.occupancy` - Relatórios de ocupação
- `reports.customers` - Relatórios de clientes

### Configurações

- `settings.view` - Visualizar configurações
- `settings.edit` - Editar configurações

### Sistema

- `permissions.manage` - Gerenciar permissões
- `dashboard.admin` - Dashboard administrativo
- `dashboard.manager` - Dashboard gerencial
- `dashboard.reception` - Dashboard recepção

### Perfil

- Não requer permissão específica - qualquer usuário logado pode editar seu próprio perfil

- `editar perfil` - Modificar dados do próprio perfil

## Implementação nos Controllers

### Usando o Trait HasPermissions

Todos os controllers da API usam o trait `HasPermissions` que fornece:

```php
// Verificar uma permissão específica (para ou retorna erro)
$this->checkPermission('apartments.view');

// Verificar múltiplas permissões (qualquer uma)
$this->checkAnyPermission(['apartments.create', 'apartments.edit']);

// Verificar múltiplas permissões (todas obrigatórias)
$this->checkAllPermissions(['apartments.view', 'apartments.edit']);

// Verificar sem parar execução
if ($this->hasPermission('apartments.delete')) {
    // fazer algo
}

// Obter todas as permissões do usuário
$permissions = $this->getCurrentUserPermissions();
```

### Exemplo de Controller

```php
class ApartamentoController extends Controller
{
    use HasPermissions;

    public function index(Request $request)
    {
        // Verifica permissão antes de continuar
        $this->checkPermission('apartments.view');

        // resto da lógica...
    }

    public function store(Request $request)
    {
        $this->checkPermission('apartments.create');

        // lógica de criação...
    }
}
```

## Resposta do Login

Quando o usuário faz login, a resposta inclui as permissões:

```json
{
  "status": 200,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": "uuid-here",
    "name": "João Silva",
    "email": "joao@hotel.com",
    "access": "recepcionista"
  },
  "permissions": [
    {
      "id": 1,
      "name": "apartments.view",
      "description": "Visualizar apartamentos"
    },
    {
      "id": 2,
      "name": "reservations.create",
      "description": "Criar novas reservas"
    }
  ]
}
```

## Tipos de Erro

### 401 Unauthorized

```json
{
  "status": 401,
  "message": "Token inválido ou expirado",
  "error": "Unauthorized"
}
```

### 403 Forbidden

```json
{
  "status": 403,
  "message": "Você não possui permissão para: apartments.create",
  "error": "Forbidden"
}
```

## Configuração de Permissões por Perfil

### Administrador

- Acesso total a todas as funcionalidades

### Gerente

- Visualizar e editar quartos, reservas, relatórios
- Gerenciar funcionários
- Configurações do sistema

### Recepcionista

- Visualizar e criar reservas
- Visualizar quartos
- Check-in/Check-out

### Caixa

- Operações de caixa
- Transações financeiras
- Relatórios de caixa

### Bar

- Registrar vendas do bar
- Comandas
- Produtos

## Middleware de Permissões

O sistema usa o `PermissionMiddleware` que:

1. Extrai o token JWT do header Authorization
2. Decodifica o token para obter dados do usuário
3. Busca as permissões do usuário no banco
4. Verifica se tem a permissão necessária
5. Permite ou nega o acesso

## Banco de Dados

As permissões são armazenadas nas tabelas:

- `permissao` - Lista de todas as permissões disponíveis
- `usuario_permissao` - Relacionamento entre usuários e permissões

## Uso no Frontend

O frontend deve:

1. Armazenar o token e permissões após login
2. Verificar permissões antes de mostrar botões/menus
3. Enviar o token em todas as requisições
4. Tratar erros 401/403 adequadamente

```javascript
// Exemplo de uso no frontend
const hasPermission = (permission) => {
  return user.permissions.some((p) => p.name === permission);
};

// Mostrar botão apenas se tiver permissão
{
  hasPermission("apartments.create") && (
    <button onClick={createRoom}>Criar Quarto</button>
  );
}
```
