# API Fluxo de Caixa - Sistema de Reservas

## Endpoints de Transações de Caixa

### 1. Listar Transações

**GET** `/api/v1/transacao-caixa`

**Parâmetros de Query:**

- `page` (opcional): Página atual (padrão: 1)
- `limit` (opcional): Itens por página (padrão: 10)
- `caixa_id` (opcional): Filtrar por ID do caixa
- `type` (opcional): Filtrar por tipo (entrada/saida)
- `origin` (opcional): Filtrar por origem (pagamento, venda, etc.)
- `payment_form` (opcional): Filtrar por forma de pagamento

**Resposta:**

```json
{
  "transacoes": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 50,
    "last_page": 5,
    "has_previous_page": false,
    "has_next_page": true
  }
}
```

### 2. Criar Nova Transação

**POST** `/api/v1/transacao-caixa`

**Body:**

```json
{
  "caixa_id": 1,
  "type": "entrada",
  "origin": "venda_produto",
  "payment_form": "Dinheiro",
  "amount": 50.0,
  "description": "Venda de bebida",
  "reference_uuid": "uuid-da-venda-opcional"
}
```

**Campos obrigatórios:**

- `caixa_id`: ID do caixa (deve estar aberto)
- `type`: "entrada" ou "saida"
- `origin`: Origem da transação
- `payment_form`: "Dinheiro", "PIX", "Cartão Crédito", "Cartão Débito", "Cortesia", "Permuta"
- `amount`: Valor da transação

**Resposta de Sucesso:**

```json
{
  "message": "Transação criada com sucesso",
  "transacao": {
    "id": 1,
    "uuid": "abc-123-def-456",
    "caixa_id": 1,
    "type": "entrada",
    "origin": "venda_produto",
    "amount": 50.0
    // ... outros campos
  }
}
```

### 3. Buscar Transação por UUID

**GET** `/api/v1/transacao-caixa/{uuid}`

**Resposta:**

```json
{
  "transacao": {
    "id": 1,
    "uuid": "abc-123-def-456",
    "caixa_id": 1,
    "type": "entrada",
    "origin": "venda_produto",
    "amount": 50.0,
    "canceled": 0
    // ... outros campos
  }
}
```

### 4. Cancelar Transação

**PUT** `/api/v1/transacao-caixa/{uuid}/cancel`

**Resposta:**

```json
{
  "message": "Transação cancelada com sucesso"
}
```

### 5. Listar Transações por Caixa

**GET** `/api/v1/transacao-caixa/caixa/{caixa_id}`

**Parâmetros de Query:**

- `page` (opcional): Página atual
- `limit` (opcional): Itens por página

### 6. Relatório de Transações

**GET** `/api/v1/transacao-caixa/relatorio`

**Parâmetros de Query obrigatórios:**

- `data_inicio`: Data inicial (YYYY-MM-DD)
- `data_fim`: Data final (YYYY-MM-DD)
- `caixa_id` (opcional): ID do caixa específico

**Resposta:**

```json
{
  "transacoes": [...],
  "resumo": {
    "total_entradas": 500.00,
    "total_saidas": 200.00,
    "total_canceladas": 50.00,
    "saldo_periodo": 300.00,
    "total_transacoes": 25
  }
}
```

## Integração com Caixa

### Triggers Automáticos

O sistema possui triggers que automaticamente:

1. **Ao inserir pagamento** (tabela `pagamentos`):

   - Cria registro na `transacao_caixa`
   - Atualiza `current_balance` do caixa (apenas para "Dinheiro")

2. **Ao atualizar pagamento**:

   - Atualiza registro correspondente na `transacao_caixa`
   - Ajusta `current_balance` conforme necessário

3. **Ao cancelar pagamento**:
   - Marca transação como cancelada
   - Reverte o saldo do caixa

### Tipos de Origem Suportados

- `pagamento`: Pagamentos de reservas/vendas (automático via trigger)
- `sangria`: Retirada de dinheiro
- `suprimento`: Entrada de dinheiro
- `venda_produto`: Venda de produtos
- `despesa`: Despesas operacionais
- `ajuste`: Ajustes manuais
- `outros`: Outras movimentações

### Formas de Pagamento

- `Dinheiro`: Afeta o saldo físico do caixa
- `PIX`: Registro contábil
- `Cartão Crédito`: Registro contábil
- `Cartão Débito`: Registro contábil
- `Cortesia`: Registro contábil
- `Permuta`: Registro contábil

## Fluxo Recomendado

1. **Abertura do Caixa**: Use a API de caixa para abrir com valor inicial
2. **Transações Automáticas**: Pagamentos geram transações automaticamente
3. **Transações Manuais**: Use a API para sangrias, suprimentos, despesas
4. **Consultas**: Use os endpoints de listagem e relatórios
5. **Fechamento**: Use a API de caixa para fechar e calcular diferenças

## Validações

- Caixa deve estar aberto para novas transações
- Apenas transações "Dinheiro" afetam o saldo físico
- Transações canceladas não podem ser canceladas novamente
- Valores devem ser positivos
- Campos obrigatórios devem ser preenchidos

## Autenticação

Todos os endpoints requerem autenticação via token JWT.
Header: `Authorization: Bearer {token}`
