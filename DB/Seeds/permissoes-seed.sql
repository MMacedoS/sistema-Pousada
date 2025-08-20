
INSERT INTO permissao (uuid, name, description, created_at, updated_at) VALUES
(UUID(), 'users.view', 'Visualizar lista de usuários', NOW(), NOW()),
(UUID(), 'users.create', 'Criar novos usuários', NOW(), NOW()),
(UUID(), 'users.edit', 'Editar dados de usuários', NOW(), NOW()),
(UUID(), 'users.delete', 'Excluir usuários', NOW(), NOW()),
(UUID(), 'reservations.view', 'Visualizar reservas', NOW(), NOW()),
(UUID(), 'reservations.create', 'Criar novas reservas', NOW(), NOW()),
(UUID(), 'reservations.edit', 'Editar reservas existentes', NOW(), NOW()),
(UUID(), 'reservations.cancel', 'Cancelar reservas', NOW(), NOW()),
(UUID(), 'reservations.checkin', 'Realizar check-in', NOW(), NOW()),
(UUID(), 'reservations.checkout', 'Realizar check-out', NOW(), NOW()),(UUID(), 'apartments.view', 'Visualizar apartamentos', NOW(), NOW()),
(UUID(), 'apartments.create', 'Cadastrar apartamentos', NOW(), NOW()),
(UUID(), 'apartments.edit', 'Editar apartamentos', NOW(), NOW()),
(UUID(), 'apartments.delete', 'Excluir apartamentos', NOW(), NOW()),
(UUID(), 'apartments.status', 'Alterar status apartamentos', NOW(), NOW()),
(UUID(), 'customers.view', 'Visualizar clientes', NOW(), NOW()),
(UUID(), 'customers.create', 'Cadastrar clientes', NOW(), NOW()),
(UUID(), 'customers.edit', 'Editar dados de clientes', NOW(), NOW()),
(UUID(), 'customers.delete', 'Excluir clientes', NOW(), NOW()),
(UUID(), 'cashbox.view', 'Visualizar movimentações caixa', NOW(), NOW()),
(UUID(), 'cashbox.open', 'Abrir caixa', NOW(), NOW()),
(UUID(), 'cashbox.close', 'Fechar caixa', NOW(), NOW()),
(UUID(), 'cashbox.transactions', 'Realizar transações', NOW(), NOW()),
(UUID(), 'financial.reports', 'Acessar relatórios financeiros', NOW(), NOW()),
(UUID(), 'sales.view', 'Visualizar vendas', NOW(), NOW()),
(UUID(), 'sales.create', 'Realizar vendas', NOW(), NOW()),
(UUID(), 'sales.cancel', 'Cancelar vendas', NOW(), NOW()),
(UUID(), 'bar.sales', 'Vendas específicas do bar', NOW(), NOW()),
(UUID(), 'bar.inventory', 'Controle estoque do bar', NOW(), NOW()),
(UUID(), 'products.view', 'Visualizar produtos', NOW(), NOW()),
(UUID(), 'products.create', 'Cadastrar produtos', NOW(), NOW()),
(UUID(), 'products.edit', 'Editar produtos', NOW(), NOW()),
(UUID(), 'products.delete', 'Excluir produtos', NOW(), NOW()),
(UUID(), 'reports.reservations', 'Relatórios de reservas', NOW(), NOW()),
(UUID(), 'reports.financial', 'Relatórios financeiros', NOW(), NOW()),
(UUID(), 'reports.occupancy', 'Relatórios de ocupação', NOW(), NOW()),
(UUID(), 'reports.customers', 'Relatórios de clientes', NOW(), NOW()),
(UUID(), 'settings.view', 'Visualizar configurações', NOW(), NOW()),
(UUID(), 'settings.edit', 'Editar configurações', NOW(), NOW()),
(UUID(), 'permissions.manage', 'Gerenciar permissões', NOW(), NOW()),
(UUID(), 'dashboard.admin', 'Dashboard administrativo', NOW(), NOW()),
(UUID(), 'dashboard.manager', 'Dashboard gerencial', NOW(), NOW()),
(UUID(), 'dashboard.reception', 'Dashboard recepção', NOW(), NOW());
(UUID(), 'employees.view', 'Visualizar funcionários', NOW(), NOW()),
(UUID(), 'employees.create', 'Cadastrar funcionários', NOW(), NOW()),
(UUID(), 'employees.edit', 'Editar funcionários', NOW(), NOW()),
(UUID(), 'employees.delete', 'Excluir funcionários', NOW(), NOW()),
(UUID(), 'employees.status', 'Alterar status funcionários', NOW(), NOW());

INSERT INTO permissao_as_usuario (permissao_id, usuario_id, created_at, updated_at)
SELECT p.id, u.id, NOW(), NOW()
FROM permissao p, usuarios u
WHERE u.email = 'admin@admin.com';
INSERT INTO permissao_as_usuario (permissao_id, usuario_id, created_at, updated_at)
SELECT p.id, u.id, NOW(), NOW()
FROM permissao p, usuarios u
WHERE u.email = 'gerente@hotel.com'
AND p.name IN (
    'users.view', 'users.create', 'users.edit',
    'reservations.view', 'reservations.create', 'reservations.edit', 'reservations.cancel', 'reservations.checkin', 'reservations.checkout',
    'apartments.view', 'apartments.create', 'apartments.edit', 'apartments.status',
    'customers.view', 'customers.create', 'customers.edit',
    'cashbox.view', 'cashbox.open', 'cashbox.close', 'financial.reports',
    'sales.view', 'sales.create', 'sales.cancel',
    'products.view', 'products.create', 'products.edit',
    'reports.reservations', 'reports.financial', 'reports.occupancy', 'reports.customers',
    'settings.view',
    'dashboard.admin', 'dashboard.manager', 'employees.view', 'employees.create', 'employees.edit', 'employees.delete', 'employees.status'
);
INSERT INTO permissao_as_usuario (permissao_id, usuario_id, created_at, updated_at)
SELECT p.id, u.id, NOW(), NOW()
FROM permissao p, usuarios u
WHERE u.email = 'recepcao@hotel.com'
AND p.name IN (
    'reservations.view', 'reservations.create', 'reservations.edit', 'reservations.checkin', 'reservations.checkout',
    'apartments.view', 'apartments.status',
    'customers.view', 'customers.create', 'customers.edit',
    'sales.view', 'sales.create',
    'reports.reservations', 'reports.occupancy',
    'dashboard.reception'
);
INSERT INTO permissao_as_usuario (permissao_id, usuario_id, created_at, updated_at)
SELECT p.id, u.id, NOW(), NOW()
FROM permissao p, usuarios u
WHERE u.email = 'caixa@hotel.com'
AND p.name IN (
    'reservations.view', 'reservations.checkout',
    'customers.view',
    'cashbox.view', 'cashbox.open', 'cashbox.close', 'cashbox.transactions',
    'sales.view', 'sales.create', 'sales.cancel',
    'products.view',
    'reports.financial',
    'dashboard.reception'
);
INSERT INTO permissao_as_usuario (permissao_id, usuario_id, created_at, updated_at)
SELECT p.id, u.id, NOW(), NOW()
FROM permissao p, usuarios u
WHERE u.email = 'bar@hotel.com'
AND p.name IN (
    'customers.view',
    'sales.view', 'sales.create', 'sales.cancel',
    'bar.sales', 'bar.inventory',
    'products.view',
    'cashbox.view', 'cashbox.transactions',
    'dashboard.reception'
);