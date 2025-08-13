# Sistema de Seeds - Hotel Reserva

Este diretório contém os arquivos SQL para popular o banco de dados com dados iniciais do sistema.

## Estrutura

```
DB/Seeds/
├── usuarios-seed.sql     # Seed SQL para criar usuários padrão
├── permissoes-seed.sql   # Seed SQL para permissões e atribuições
├── DOCKER.md            # Instruções específicas para Docker
└── README.md            # Esta documentação
```

## Como Executar

### Método 1: Via Cliente MySQL

```bash
# Execute primeiro os usuários, depois as permissões:
mysql -u root -p banco < DB/Seeds/usuarios-seed.sql
mysql -u root -p banco < DB/Seeds/permissoes-seed.sql

# Ou via Docker:
docker exec -i SEU_CONTAINER_MYSQL mysql -u root -p banco < DB/Seeds/usuarios-seed.sql
docker exec -i SEU_CONTAINER_MYSQL mysql -u root -p banco < DB/Seeds/permissoes-seed.sql
```

### Método 2: Via phpMyAdmin

1. Acesse o phpMyAdmin
2. Selecione o banco de dados
3. Vá na aba "SQL"
4. **Execute primeiro:** Copie e cole o conteúdo do arquivo `usuarios-seed.sql`
5. Execute
6. **Execute depois:** Copie e cole o conteúdo do arquivo `permissoes-seed.sql`
7. Execute

### Método 3: Via Docker Compose

```bash
# Se você usa docker-compose com MySQL
docker-compose exec mysql mysql -u root -p banco < DB/Seeds/usuarios-seed.sql
docker-compose exec mysql mysql -u root -p banco < DB/Seeds/permissoes-seed.sql
```

### Método 4: Via linha de comando no container

```bash
# Entre no container MySQL
docker exec -it SEU_CONTAINER_MYSQL bash

# Execute os SQLs em ordem
mysql -u root -p banco < /path/to/usuarios-seed.sql
mysql -u root -p banco < /path/to/permissoes-seed.sql
```

## Usuários Criados

O arquivo `usuarios-seed.sql` cria os seguintes usuários padrão:

| Nome                     | Email              | Senha    | Nível de Acesso   |
| ------------------------ | ------------------ | -------- | ----------------- |
| Administrador do Sistema | admin@admin.com    | password | administrador     |
| Gerente Operacional      | gerente@hotel.com  | password | gerente           |
| Recepcionista Principal  | recepcao@hotel.com | password | recepcionista     |
| Operador de Caixa        | caixa@hotel.com    | password | recepcionista     |
| Recepcionista do Bar     | bar@hotel.com      | password | recepcionista_bar |

**Nota:** A senha padrão é `password` para todos os usuários. Altere as senhas após o primeiro login.

## 🔐 Sistema de Permissões

O arquivo `permissoes-seed.sql` cria **44 permissões organizadas por módulos**:

### 📋 **Módulos de Permissões:**

- **👥 Usuários**: Visualizar, criar, editar, excluir
- **🏨 Reservas**: CRUD completo + check-in/out
- **🏠 Apartamentos**: Gestão completa + status
- **👤 Clientes**: CRUD completo
- **💰 Financeiro/Caixa**: Abertura, fechamento, transações
- **🛒 Vendas/Bar**: Vendas gerais + específicas do bar
- **📦 Produtos**: CRUD completo
- **📊 Relatórios**: Reservas, financeiro, ocupação
- **⚙️ Configurações**: Sistema + permissões
- **📈 Dashboard**: Admin, gerencial, recepção

### 🎯 **Perfis de Acesso:**

| Perfil            | Permissões | Foco Principal                  |
| ----------------- | ---------- | ------------------------------- |
| **Administrador** | 44 (todas) | Controle total do sistema       |
| **Gerente**       | 29         | Gestão operacional e relatórios |
| **Recepcionista** | 11         | Reservas e atendimento          |
| **Caixa**         | 12         | Financeiro e vendas             |
| **Bar**           | 9          | Vendas do bar e estoque         |

## Características dos Seeds

- ✅ **SQL Puro**: Arquivo SQL simples, funciona com qualquer cliente MySQL
- ✅ **Idempotente**: Pode ser executado múltiplas vezes (adicione IF NOT EXISTS se necessário)
- ✅ **Completo**: Cria tanto registros em `usuarios` quanto em `pessoa_fisica`
- ✅ **UUIDs**: Utiliza UUID() do MySQL para gerar identificadores únicos
- ✅ **Relacionamentos**: Mantém integridade entre usuários e pessoas físicas

## Personalizando

Para criar novos usuários, adicione linhas no arquivo SQL seguindo o padrão:

```sql
INSERT INTO usuarios (uuid, name, email, access, password, active, created_at, updated_at) VALUES
(UUID(), 'Nome do Usuário', 'email@exemplo.com', 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());

INSERT INTO pessoa_fisica (uuid, usuario_id, name, social_name, email, birthday, doc, type_doc, phone, address, created_at, updated_at)
SELECT UUID(), u.id, 'Nome do Usuário', 'Nome Social', 'email@exemplo.com', '1990-01-01', '12345678901', 'cpf', '(11) 99999-9999', 'Endereço Completo', NOW(), NOW()
FROM usuarios u WHERE u.email = 'email@exemplo.com';
```

## Requisitos

- MySQL/MariaDB
- Banco de dados criado (conforme estrutura.sql)
- Acesso ao banco via cliente MySQL, phpMyAdmin ou Docker

## Observações

- Execute os seeds **APÓS** importar o arquivo `DB/estrutura.sql`
- **ORDEM IMPORTANTE:** Execute primeiro `usuarios-seed.sql`, depois `permissoes-seed.sql`
- Os arquivos usam funções nativas do MySQL (UUID(), NOW(), etc.)
- As senhas estão hasheadas com bcrypt (hash da palavra "password")
- Todos os usuários são criados como ativos (active = 1)
- As permissões são atribuídas automaticamente baseadas no perfil do usuário
- Sistema de permissões granular permite controle fino de acesso
