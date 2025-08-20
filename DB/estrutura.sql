-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 18/08/2025 às 11:55
-- Versão do servidor: 8.0.41
-- Versão do PHP: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `apartamentos`
--

CREATE TABLE `apartamentos` (
  `id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` enum('Casal','Solteiro','Triplo','Quadruplo','Suite','Chale') NOT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `situation` enum('Impedido','Disponivel','Ocupado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Disponivel',
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome_original` varchar(100) NOT NULL,
  `path` varchar(255) NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `ext_arquivo` varchar(6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caixas`
--

CREATE TABLE `caixas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `id_usuario_opened` int NOT NULL,
  `id_usuario_closed` int DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `initial_amount` decimal(10,2) NOT NULL,
  `current_balance` decimal(10,2) DEFAULT '0.00',
  `final_amount` decimal(10,2) DEFAULT NULL,
  `difference` decimal(10,2) DEFAULT NULL,
  `status` enum('aberto','fechado','cancelado') NOT NULL DEFAULT 'aberto',
  `obs` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Acionadores `caixas`
--
DELIMITER $$
CREATE TRIGGER `prevent_multiple_open_caixas_insert` BEFORE INSERT ON `caixas` FOR EACH ROW BEGIN
    IF NEW.status = 'aberto' THEN
        IF EXISTS (
            SELECT 1
            FROM caixas
            WHERE id_usuario_opened = NEW.id_usuario_opened
              AND status = 'aberto'
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'O usuário já possui um caixa aberto.';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_multiple_open_caixas_update` BEFORE UPDATE ON `caixas` FOR EACH ROW BEGIN
    IF NEW.status = 'aberto' AND OLD.status <> 'aberto' THEN
        IF EXISTS (
            SELECT 1
            FROM caixas
            WHERE id_usuario_opened = NEW.id_usuario_opened
              AND status = 'aberto'
              AND id <> NEW.id
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'O usuário já possui um caixa aberto.';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `job` varchar(30) DEFAULT NULL,
  `nationality` varchar(30) DEFAULT NULL,
  `doc` varchar(20) DEFAULT NULL,
  `type_doc` varchar(20) DEFAULT NULL,
  `representative` tinyint DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `cnpj_company` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone_company` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email_company` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracao`
--

CREATE TABLE `configuracao` (
  `id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address` varchar(255) NOT NULL,
  `checkin` time NOT NULL DEFAULT '14:00:00',
  `checkout` time NOT NULL DEFAULT '12:00:00',
  `percentage_service_fee` float NOT NULL DEFAULT '10',
  `cleaning_rate` float NOT NULL DEFAULT '30',
  `allow_booking_online` tinyint NOT NULL DEFAULT '1',
  `cancellation_policies` text,
  `currency` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'BRL',
  `time_zone` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'America/Bahia',
  `advance_booking_days` int DEFAULT '180',
  `display_values_on_dashboard` tinyint DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consumos`
--

CREATE TABLE `consumos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `id_reserva` int NOT NULL,
  `id_produto` int NOT NULL,
  `quantity` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_usuario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Acionadores `consumos`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_consumos` AFTER INSERT ON `consumos` FOR EACH ROW UPDATE estoque SET quantity = (quantity - new.quantity) where id_produto = new.id_produto
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_consumos` AFTER UPDATE ON `consumos` FOR EACH ROW BEGIN
    IF NEW.status = '1' THEN
        UPDATE estoque SET quantity = ((quantity + OLD.quantity) - NEW.quantity) WHERE id_produto = OLD.id_produto;
    END IF;

    IF NEW.status = '0' THEN
        UPDATE estoque SET quantity = (quantity + OLD.quantity) WHERE id_produto = OLD.id_produto;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diarias`
--

CREATE TABLE `diarias` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `id_reserva` int DEFAULT NULL,
  `dt_daily` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `id_usuario` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int NOT NULL,
  `id_produto` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `id_venda` int DEFAULT NULL,
  `id_produto` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `quantity` int DEFAULT NULL,
  `amount_item` decimal(10,2) DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Acionadores `itens_venda`
--
DELIMITER $$
CREATE TRIGGER `trg_delete_itens` AFTER DELETE ON `itens_venda` FOR EACH ROW UPDATE estoque SET quantity = (quantity + OLD.quantity) WHERE id_produto = OLD.id_produto
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_insert_itens` AFTER INSERT ON `itens_venda` FOR EACH ROW UPDATE estoque SET quantity = (quantity - new.quantity) where id_produto = new.id_produto
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_itens` AFTER UPDATE ON `itens_venda` FOR EACH ROW BEGIN
IF NEW.status = '1' THEN
        UPDATE estoque SET quantity = ((quantity + OLD.quantity) - NEW.quantity) WHERE id_produto = OLD.id_produto;
    END IF;

    IF NEW.status = '0' THEN
        UPDATE estoque SET quantity = (quantity + OLD.quantity) WHERE id_produto = OLD.id_produto;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mesas`
--

CREATE TABLE `mesas` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `name` int NOT NULL,
  `status` enum('livre','ocupada','fechada') DEFAULT 'livre',
  `id_venda` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `id_reserva` int DEFAULT NULL,
  `type_payment` enum('Dinheiro','Cartão Crédito','Cartão Débito','Pix','Cortesia') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `dt_payment` date NOT NULL,
  `id_venda` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `id_usuario` int DEFAULT NULL,
  `id_caixa` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Acionadores `pagamentos`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_pagamentos` AFTER INSERT ON `pagamentos` FOR EACH ROW BEGIN
INSERT INTO transacao_caixa SET 
    uuid = NEW.uuid, 
    caixa_id = NEW.id_caixa, 
    type = "entrada", 
    amount = NEW.payment_amount, 
    payment_form = NEW.type_payment,
    origin = "pagamento",
    reference_uuid = NEW.uuid,
    description = CONCAT("Pagamento ID: ", NEW.id, " - ", NEW.type_payment),
    id_usuario = NEW.id_usuario,
    canceled = 0; 
    
IF NEW.type_payment='Dinheiro' THEN
    UPDATE caixas SET current_balance = (current_balance + NEW.payment_amount) WHERE id = NEW.id_caixa;
END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_pagamentos` AFTER UPDATE ON `pagamentos` FOR EACH ROW BEGIN
    -- Atualiza a tabela transacao_caixa com as informações do pagamento
    UPDATE transacao_caixa 
    SET caixa_id = NEW.id_caixa,
        type = "entrada",
        amount = NEW.payment_amount,
        payment_form = NEW.type_payment,
        id_usuario = NEW.id_usuario,
        canceled = CASE WHEN NEW.status = 0 THEN 1 ELSE 0 END,
        description = CONCAT("Pagamento ID: ", NEW.id, " - ", NEW.type_payment)
    WHERE reference_uuid = OLD.uuid AND origin = "pagamento"; 

    -- Caso o pagamento seja cancelado (status = 0) ou o tipo de pagamento tenha sido alterado de "Dinheiro" para outro
    IF (NEW.status = 0 AND OLD.type_payment = 'Dinheiro') 
       OR (NEW.status = 1 AND OLD.type_payment = 'Dinheiro' AND NEW.type_payment != 'Dinheiro') THEN
        UPDATE caixas 
        SET current_balance = current_balance - OLD.payment_amount 
        WHERE id = OLD.id_caixa;
    END IF;

    -- Caso o pagamento seja confirmado (status = 1) ou o tipo de pagamento tenha sido alterado de outro para "Dinheiro"
    IF (NEW.status = 1 AND NEW.type_payment = 'Dinheiro' AND OLD.type_payment != 'Dinheiro') 
       OR (OLD.status = 0 AND NEW.status = 1 AND NEW.type_payment = 'Dinheiro') THEN
        UPDATE caixas 
        SET current_balance = current_balance + NEW.payment_amount
        WHERE id = NEW.id_caixa;
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao`
--

CREATE TABLE `permissao` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao_as_usuario`
--

CREATE TABLE `permissao_as_usuario` (
  `id` int NOT NULL,
  `permissao_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_fisica`
--

CREATE TABLE `pessoa_fisica` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `usuario_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `social_name` varchar(45) DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `email` varchar(100) NOT NULL,
  `doc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `type_doc` enum('CPF','RG','PASSPORT','CNH') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'CPF',
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(40) NOT NULL,
  `stock` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_usuario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `id_apartamento` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `dt_checkin` date DEFAULT NULL,
  `dt_checkout` date DEFAULT NULL,
  `status` enum('Reservada','Confirmada','Hospedada','Finalizada','Cancelada','Apagada') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Reservada',
  `amount` decimal(7,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reserva_hospedes`
--

CREATE TABLE `reserva_hospedes` (
  `id_reserva` int NOT NULL,
  `id_hospede` int NOT NULL,
  `is_primary` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacao_caixa`
--

CREATE TABLE `transacao_caixa` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `caixa_id` int NOT NULL,
  `type` enum('entrada','saida','sangria') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `reference_uuid` char(36) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_form` enum('Dinheiro','PIX','Cartão de Crédito','Cartão de Débito','Cortesia','Permuta','Transferência Bancária','Boleto') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `canceled` tinyint NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `arquivo_id` int DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `send_access` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `access` enum('administrador','gerente','recepcionista','recepcionista_bar') NOT NULL DEFAULT 'recepcionista',
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `dt_sale` date NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount_sale` decimal(7,2) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `id_reserva` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `apartamentos`
--
ALTER TABLE `apartamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `active` (`active`);

--
-- Índices de tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caixas`
--
ALTER TABLE `caixas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario_abertura` (`id_usuario_opened`),
  ADD KEY `id_usuario_fechamento` (`id_usuario_closed`),
  ADD KEY `idx_caixa_uuid` (`uuid`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uuid` (`uuid`,`name`,`email`,`status`);

--
-- Índices de tabela `configuracao`
--
ALTER TABLE `configuracao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consumos`
--
ALTER TABLE `consumos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reserva` (`id_reserva`),
  ADD KEY `id_produto` (`id_produto`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `diarias`
--
ALTER TABLE `diarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_reserva` (`id_reserva`,`dt_daily`,`status`,`amount`) USING BTREE,
  ADD KEY `uuid` (`uuid`,`dt_daily`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venda` (`id_venda`),
  ADD KEY `id_produto` (`id_produto`),
  ADD KEY `fk_items_vendas_usuario` (`id_usuario`);

--
-- Índices de tabela `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reserva` (`id_reserva`),
  ADD KEY `fk_venda_paga` (`id_venda`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `caixa_fk` (`id_caixa`);

--
-- Índices de tabela `permissao`
--
ALTER TABLE `permissao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `name_2` (`name`),
  ADD KEY `name` (`name`);

--
-- Índices de tabela `permissao_as_usuario`
--
ALTER TABLE `permissao_as_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unicos` (`permissao_id`,`usuario_id`),
  ADD KEY `fk_permissao` (`permissao_id`),
  ADD KEY `fk_ussuario` (`usuario_id`);

--
-- Índices de tabela `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `dados_unicos` (`usuario_id`,`name`,`email`,`doc`),
  ADD KEY `fk_pessoa_fisica_usuario` (`usuario_id`),
  ADD KEY `ativo` (`active`) USING BTREE,
  ADD KEY `data_nascimento` (`birthday`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `uuid_2` (`uuid`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `category` (`category`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_apartamento` (`id_apartamento`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `status` (`status`),
  ADD KEY `dt_checkin` (`dt_checkin`,`dt_checkout`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `reserva_hospedes`
--
ALTER TABLE `reserva_hospedes`
  ADD PRIMARY KEY (`id_reserva`,`id_hospede`),
  ADD KEY `id_hospede` (`id_hospede`);

--
-- Índices de tabela `transacao_caixa`
--
ALTER TABLE `transacao_caixa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `idx_transacao_caixa_id` (`caixa_id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `reference_uuid` (`reference_uuid`),
  ADD KEY `origin` (`origin`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`active`),
  ADD KEY `fk_arquivo_usuario` (`arquivo_id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `status` (`status`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `reserva_fk` (`id_reserva`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `apartamentos`
--
ALTER TABLE `apartamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `arquivos`
--
ALTER TABLE `arquivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caixas`
--
ALTER TABLE `caixas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consumos`
--
ALTER TABLE `consumos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `diarias`
--
ALTER TABLE `diarias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissao`
--
ALTER TABLE `permissao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissao_as_usuario`
--
ALTER TABLE `permissao_as_usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transacao_caixa`
--
ALTER TABLE `transacao_caixa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `caixas`
--
ALTER TABLE `caixas`
  ADD CONSTRAINT `caixas_ibfk_1` FOREIGN KEY (`id_usuario_opened`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `caixas_ibfk_2` FOREIGN KEY (`id_usuario_closed`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `consumos`
--
ALTER TABLE `consumos`
  ADD CONSTRAINT `fk_consumo_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_consumo_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_consumo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `diarias`
--
ALTER TABLE `diarias`
  ADD CONSTRAINT `fk_diarias_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_diarias_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `fk_items_venda` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_items_venda_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_items_vendas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_pagamentos_caixa` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_pagamentos_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_pagamentos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_pagamentos_venda` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `permissao_as_usuario`
--
ALTER TABLE `permissao_as_usuario`
  ADD CONSTRAINT `fk_permissao_as_usuario_permissao` FOREIGN KEY (`permissao_id`) REFERENCES `permissao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_permissao_as_usuario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  ADD CONSTRAINT `fk_pessoa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produtos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_apartamento` FOREIGN KEY (`id_apartamento`) REFERENCES `apartamentos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `reserva_hospedes`
--
ALTER TABLE `reserva_hospedes`
  ADD CONSTRAINT `fk_reserva_hospede_cliente` FOREIGN KEY (`id_hospede`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_reserva_hospede_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `transacao_caixa`
--
ALTER TABLE `transacao_caixa`
  ADD CONSTRAINT `fk_transacao_caixa_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `transacao_caixa_ibfk_1` FOREIGN KEY (`caixa_id`) REFERENCES `caixas` (`id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_arquivo_usuario` FOREIGN KEY (`arquivo_id`) REFERENCES `arquivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_vendas_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_vendas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
