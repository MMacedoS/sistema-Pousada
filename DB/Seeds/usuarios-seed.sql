
INSERT INTO usuarios (uuid, name, email, access, password, active, created_at, updated_at) VALUES
(UUID(), 'Administrador do Sistema', 'admin@admin.com', 'administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(UUID(), 'Gerente Operacional', 'gerente@hotel.com', 'gerente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(UUID(), 'Recepcionista Principal', 'recepcao@hotel.com', 'recepcionista', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(UUID(), 'Operador de Caixa', 'caixa@hotel.com', 'recepcionista', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(UUID(), 'Recepcionista do Bar', 'bar@hotel.com', 'recepcionista_bar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());

INSERT INTO pessoa_fisica (uuid, usuario_id, name, social_name, email, birthday, doc, type_doc, phone, address, created_at, updated_at) 
SELECT 
    UUID() as uuid,
    u.id as usuario_id,
    u.name,
    CASE 
        WHEN u.email = 'admin@admin.com' THEN 'Administrador'
        WHEN u.email = 'gerente@hotel.com' THEN 'Gerente'
        WHEN u.email = 'recepcao@hotel.com' THEN 'Recepcionista'
        WHEN u.email = 'caixa@hotel.com' THEN 'Caixa'
        WHEN u.email = 'bar@hotel.com' THEN 'Barman'
    END as social_name,
    u.email,
    CASE 
        WHEN u.email = 'admin@admin.com' THEN '1990-01-01'
        WHEN u.email = 'gerente@hotel.com' THEN '1985-05-15'
        WHEN u.email = 'recepcao@hotel.com' THEN '1992-08-20'
        WHEN u.email = 'caixa@hotel.com' THEN '1988-12-10'
        WHEN u.email = 'bar@hotel.com' THEN '1987-09-30'
    END as birthday,
    CASE 
        WHEN u.email = 'admin@admin.com' THEN '12345678901'
        WHEN u.email = 'gerente@hotel.com' THEN '98765432109'
        WHEN u.email = 'recepcao@hotel.com' THEN '45678912345'
        WHEN u.email = 'caixa@hotel.com' THEN '78912345678'
        WHEN u.email = 'bar@hotel.com' THEN '65432109876'
    END as doc,
    'cpf' as type_doc,
    CASE 
        WHEN u.email = 'admin@admin.com' THEN '(11) 99999-9999'
        WHEN u.email = 'gerente@hotel.com' THEN '(11) 88888-8888'
        WHEN u.email = 'recepcao@hotel.com' THEN '(11) 77777-7777'
        WHEN u.email = 'caixa@hotel.com' THEN '(11) 66666-6666'
        WHEN u.email = 'bar@hotel.com' THEN '(11) 44444-4444'
    END as phone,
    CASE 
        WHEN u.email = 'admin@admin.com' THEN 'Rua dos Administradores, 123'
        WHEN u.email = 'gerente@hotel.com' THEN 'Rua dos Gerentes, 456'
        WHEN u.email = 'recepcao@hotel.com' THEN 'Rua da Recepção, 789'
        WHEN u.email = 'caixa@hotel.com' THEN 'Rua do Caixa, 321'
        WHEN u.email = 'bar@hotel.com' THEN 'Rua do Bar, 987'
    END as address,
    NOW() as created_at,
    NOW() as updated_at
FROM usuarios u 
WHERE u.email IN ('admin@admin.com', 'gerente@hotel.com', 'recepcao@hotel.com', 'caixa@hotel.com', 'bar@hotel.com');

## NOTA: A senha padrão 'password' está hasheada com bcrypt
## Para alterar as senhas, use o sistema ou gere novos hashes
