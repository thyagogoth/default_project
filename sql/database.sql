-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 11/11/2020 às 12:52
-- Versão do servidor: 10.4.14-MariaDB
-- Versão do PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `database`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato_assunto`
--

CREATE TABLE `contato_assunto` (
  `id` bigint(6) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `assunto` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `contato_assunto`
--

INSERT INTO `contato_assunto` (`id`, `ordem`, `assunto`, `slug`, `email`, `ativo`, `created_at`, `updated_at`) VALUES
(3, 1, 'Dúvidas em geral', 'duvidas-em-geral', 'mail-sample@sejaprime.com.br', 'Y', '2019-10-02 23:53:44', '2019-10-02 23:53:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato_mensagem`
--

CREATE TABLE `contato_mensagem` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `origem` int(11) NOT NULL DEFAULT 0,
  `assunto` bigint(6) UNSIGNED NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `lida` enum('Y','N') DEFAULT 'N',
  `important` enum('Y','N') NOT NULL DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE `empresa` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `descricao` mediumtext DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `site` enum('pt_BR','en_US','es_ES') DEFAULT 'pt_BR',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `galeria` enum('Y','N') DEFAULT 'Y'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `empresa_imagem` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `empresa` bigint(6) UNSIGNED NOT NULL,
  `ordem` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `imagem` varchar(255) DEFAULT NULL,
  `legenda` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `noticia` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `categoria` bigint(6) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `slug` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `chamada` varchar(255) COLLATE latin1_general_ci DEFAULT '0',
  `video` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET latin1 DEFAULT NULL,
  `tags` text COLLATE latin1_general_ci DEFAULT NULL,
  `destaque` enum('Y','N') CHARACTER SET latin1 DEFAULT 'N',
  `galeria` enum('Y','N') COLLATE latin1_general_ci DEFAULT 'Y',
  `share` enum('Y','N') COLLATE latin1_general_ci DEFAULT 'Y',
  `programado` enum('Y','N') COLLATE latin1_general_ci DEFAULT 'N',
  `data_inicio` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `data_termino` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `ativo` enum('Y','N') CHARACTER SET latin1 DEFAULT 'Y',
  `view` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticia_imagem`
--

CREATE TABLE `noticia_imagem` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `ordem` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `noticia` bigint(6) UNSIGNED NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

CREATE TABLE `revenda_recuperar_senha` (
  `id` bigint(6) NOT NULL,
  `token` char(32) NOT NULL,
  `email` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `sistema_acesso` (
  `id` bigint(6) NOT NULL,
  `sistema_usuario` bigint(6) NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sistema_action` (
  `id` int(11) NOT NULL,
  `ordem` int(11) DEFAULT 1,
  `modulo` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `restrito` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sistema_action` (`id`, `ordem`, `modulo`, `action`, `label`, `ativo`, `restrito`, `created_at`, `updated_at`) VALUES
(1, 26, 3, 'modulos', 'Gerenciar módulos', 'Y', 'Y', '2019-09-28 17:15:01', '2019-10-02 02:45:16'),
(2, 25, 3, 'cadastrar-modulo', 'Cadastrar módulo', 'Y', 'Y', '2019-09-28 17:19:36', '2019-10-02 02:45:16'),
(3, 24, 3, 'excluir-modulo', 'Excluir módulo', 'Y', 'Y', '2019-09-28 17:37:29', '2019-10-02 02:45:16'),
(4, 23, 3, 'acoes', 'Ações', 'Y', 'Y', '2019-09-28 17:38:25', '2019-10-02 02:45:16'),
(5, 22, 3, 'cadastrar-acao', 'Cadastrar ação', 'Y', 'Y', '2019-09-28 17:38:42', '2019-10-02 02:45:16'),
(6, 21, 3, 'excluir-acao', 'Excluir ação', 'Y', 'Y', '2019-09-28 17:39:03', '2019-10-02 02:45:16'),
(7, 20, 3, 'permissoes', 'Permissões', 'Y', 'Y', '2019-09-28 17:42:44', '2019-10-02 02:45:16'),
(8, 19, 3, 'cadastrar-permissao', 'Cadastrar permissão', 'Y', 'Y', '2019-09-28 18:05:04', '2019-10-02 02:45:16'),
(9, 18, 3, 'excluir-permissao', 'Excluir permissão', 'Y', 'Y', '2019-09-28 18:05:18', '2019-10-02 02:45:16'),
(10, 17, 3, 'usuarios', 'Usuários', 'Y', 'Y', '2019-09-28 18:05:49', '2019-10-02 02:45:16'),
(11, 16, 3, 'cadastrar-usuario', 'Cadastrar usuário', 'Y', 'Y', '2019-09-28 18:06:07', '2019-10-02 02:45:16'),
(12, 15, 3, 'excluir-usuario', 'Excluir usuário', 'Y', 'Y', '2019-09-28 18:06:23', '2019-10-02 02:45:16'),
(13, 14, 3, 'menu', 'Edição de menu', 'Y', 'Y', '2019-09-28 18:06:56', '2019-10-02 02:45:16'),
(14, 13, 3, 'js-update-menu-order', 'jsUpdateMenu', 'Y', 'N', '2019-09-28 18:07:33', '2019-10-03 02:00:22'),
(15, 12, 3, 'excluir-item-menu', 'Excluir item do menu', 'Y', 'Y', '2019-09-28 18:07:54', '2019-10-02 02:45:16'),
(16, 11, 3, 'ajax-get-menu', 'AjaxGetMenu', 'Y', 'N', '2019-09-28 18:08:12', '2019-10-03 01:59:05'),
(17, 10, 5, 'assuntos', 'Gerenciar departamentos', 'Y', 'Y', '2019-09-29 14:24:43', '2019-10-02 02:45:16'),
(18, 9, 5, 'cadastrar-assunto', 'Cadastrar departamento', 'Y', 'Y', '2019-09-29 14:25:26', '2019-10-02 02:45:16'),
(19, 8, 5, 'mensagens', 'Mensagens recebidas', 'Y', 'Y', '2019-09-29 14:26:21', '2019-10-02 02:45:16'),
(20, 7, 5, 'visualizar', 'Visualizar mensagem', 'Y', 'Y', '2019-09-29 14:26:41', '2019-10-02 02:45:16'),
(21, 6, 5, 'excluir-assunto', 'Excluir assunto', 'Y', 'Y', '2019-09-29 14:30:45', '2019-10-02 02:45:16'),
(22, 5, 5, 'excluir-mensagem', 'Excluir mensagem', 'Y', 'Y', '2019-09-29 14:30:59', '2019-10-02 02:45:16'),
(26, 1, 6, 'cadastrar', 'Cadastrar conteúdo', 'Y', 'Y', '2019-09-29 15:37:01', '2019-10-02 02:45:16'),
(30, 1, 6, 'get-imagens', 'ajaxGetImagens', 'Y', 'N', '2019-09-30 00:08:20', '2020-10-01 03:05:43'),
(31, 1, 6, 'ajax-remove-imagem', 'ajaxRemoveImagem', 'Y', 'N', '2019-09-30 00:09:35', '2019-10-03 01:59:46'),
(32, 1, 6, 'editar-legenda', 'Editar legendas', 'Y', 'Y', '2019-10-01 01:08:31', '2019-10-02 03:29:49'),
(33, 1, 7, 'planos', 'Gerenciar Planos', 'Y', 'Y', '2019-10-26 01:06:21', '2019-10-26 03:06:51'),
(34, 1, 7, 'excluir', 'Excluir Módulo', 'Y', 'Y', '2019-10-26 01:06:36', '2019-10-26 03:06:36'),
(35, 1, 7, 'cadastrar', 'Cadastrar plano', 'Y', 'Y', '2019-10-26 01:07:06', '2019-10-26 03:07:14'),
(36, 1, 8, 'noticias', 'Gerenciar Notícias', 'Y', 'Y', '2019-10-27 22:36:40', '2019-10-28 00:36:40'),
(37, 1, 8, 'cadastrar', 'Cadastrar Notícia', 'Y', 'Y', '2019-10-27 22:36:54', '2019-10-28 00:36:54'),
(38, 1, 8, 'excluir', 'Excluir Notícia', 'Y', 'Y', '2019-10-27 22:37:05', '2019-10-28 00:37:05'),
(39, 1, 9, 'revendas', 'Gerenciar revendas', 'Y', 'Y', '2019-11-07 23:14:54', '2019-11-08 01:14:54'),
(40, 1, 9, 'cadastrar', 'Cadastrar revenda', 'Y', 'Y', '2019-11-07 23:15:30', '2019-11-08 01:15:30'),
(41, 1, 9, 'excluir', 'Excluir revenda', 'Y', 'Y', '2019-11-07 23:15:58', '2019-11-08 01:15:58'),
(42, 1, 10, 'regioes', 'Gerenciar Regiões', 'Y', 'Y', '2019-11-11 00:29:31', '2019-11-11 02:29:31'),
(43, 1, 10, 'excluir', 'Excluir uma região', 'Y', 'Y', '2019-11-11 00:29:47', '2019-11-11 02:29:47'),
(44, 1, 10, 'cadastrar', 'Cadastrar uma região', 'Y', 'Y', '2019-11-11 00:30:02', '2019-11-11 02:30:02'),
(45, 1, 8, 'get-imagens', 'ajaxGetImages', 'Y', 'N', '2020-09-30 23:57:40', '2020-10-01 03:05:34');

CREATE TABLE `sistema_action_permissao` (
  `id` int(11) NOT NULL,
  `action` int(11) DEFAULT NULL,
  `permissao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sistema_action_permissao` (`id`, `action`, `permissao`) VALUES
(1181, 28, 1),
(1182, 36, 1),
(1183, 37, 1),
(1184, 38, 1),
(1185, 17, 1),
(1186, 18, 1),
(1187, 21, 1),
(1188, 19, 1),
(1189, 20, 1),
(1190, 22, 1),
(1191, 33, 1),
(1192, 35, 1),
(1193, 42, 1),
(1194, 43, 1),
(1195, 44, 1),
(1196, 39, 1),
(1197, 1, 1),
(1198, 2, 1),
(1199, 3, 1),
(1200, 4, 1),
(1201, 5, 1),
(1202, 6, 1),
(1203, 7, 1),
(1204, 8, 1),
(1205, 9, 1),
(1206, 10, 1),
(1207, 11, 1),
(1208, 12, 1),
(1209, 16, 1),
(1210, 13, 1),
(1211, 14, 1),
(1212, 15, 1);

CREATE TABLE `sistema_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario` bigint(20) UNSIGNED NOT NULL,
  `pk` bigint(20) UNSIGNED DEFAULT NULL,
  `tabela` varchar(255) DEFAULT NULL,
  `campo` varchar(255) DEFAULT NULL,
  `valor_anterior` longtext DEFAULT NULL,
  `valor_novo` longtext DEFAULT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `tipo` enum('Adicionar','Atualizar','Remover') DEFAULT 'Atualizar',
  `data` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Registra movimentação de cada tabela, por usuário.';

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_menu`
--

CREATE TABLE `sistema_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 1,
  `action` int(10) UNSIGNED DEFAULT 0,
  `menu` int(11) DEFAULT 0,
  `item` varchar(255) DEFAULT NULL,
  `icone` varchar(255) DEFAULT NULL,
  `oculto` enum('N','Y') DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `sistema_menu`
--

INSERT INTO `sistema_menu` (`id`, `ordem`, `action`, `menu`, `item`, `icone`, `oculto`, `created_at`, `updated_at`) VALUES
(1, 12, 0, 0, 'Sistema', 'ni ni-settings', 'N', '2019-09-29 13:57:41', '2020-10-12 13:36:31'),
(2, 501, 1, 1, 'Módulos', 'ni ni-puzzle', 'N', '2019-09-29 13:58:53', '2020-10-03 16:41:04'),
(3, 502, 2, 1, 'Cadastrar Módulo', '', 'Y', '2019-09-29 13:59:53', '2020-10-03 16:41:04'),
(4, 503, 3, 1, 'Excluir Módulo', '', 'Y', '2019-09-29 14:01:18', '2020-10-03 16:41:04'),
(6, 504, 4, 1, 'Ações', 'ni ni-puzzle', 'N', '2019-09-29 14:02:28', '2020-10-03 16:41:04'),
(8, 505, 5, 1, 'Cadastrar Ações', '', 'Y', '2019-09-29 14:07:53', '2020-10-03 16:41:04'),
(9, 506, 6, 1, 'Excluir Ação', '', 'Y', '2019-09-29 14:08:25', '2020-10-03 16:41:04'),
(10, 507, 7, 1, 'Permissões', 'ni ni-puzzle', 'N', '2019-09-29 14:02:28', '2020-10-03 16:41:04'),
(12, 508, 8, 1, 'Cadastrar Permissão', '', 'Y', '2019-09-29 14:14:34', '2020-10-03 16:41:04'),
(13, 509, 9, 1, 'Excluir Permissão', '', 'Y', '2019-09-29 14:14:46', '2020-10-03 16:41:04'),
(14, 510, 10, 1, 'Usuários', 'ni ni-users', 'N', '2019-09-29 14:18:28', '2020-10-03 16:41:04'),
(16, 511, 11, 1, 'Cadastrar Usuário', '', 'Y', '2019-09-29 14:19:06', '2020-10-03 16:41:04'),
(17, 512, 12, 1, 'Excluir Usuário', '', 'Y', '2019-09-29 14:19:27', '2020-10-03 16:41:04'),
(18, 513, 16, 1, 'AjaxGetMenu', NULL, 'Y', '2019-09-29 14:20:28', '2020-10-03 16:41:04'),
(19, 514, 13, 1, 'Montagem do Menu', 'ni ni-menu', 'N', '2019-09-29 14:21:27', '2020-10-03 16:41:04'),
(20, 515, 14, 1, 'jsUpdateMenu', NULL, 'Y', '2019-09-29 14:22:31', '2020-10-03 16:41:04'),
(21, 5, 0, 0, 'Fale conosco', 'ni ni-bubbles', 'N', '2019-09-29 14:27:49', '2020-10-03 17:12:52'),
(22, 501, 17, 21, 'Cxs. de entrada', 'fal fa-inbox', 'N', '2019-09-29 14:28:57', '2020-10-03 16:41:04'),
(23, 502, 18, 21, 'Cadastrar Assunto', '', 'Y', '2019-09-29 14:29:47', '2020-10-03 16:41:04'),
(24, 503, 21, 21, 'Excluir Assunto', '', 'Y', '2019-09-29 14:31:36', '2020-10-03 16:41:04'),
(25, 504, 19, 21, 'Mensagens', 'ni ni-speech', 'N', '2019-09-29 14:41:03', '2020-10-03 16:41:04'),
(26, 505, 20, 21, 'Visualizar Mensagem', '', 'Y', '2019-09-29 14:41:24', '2020-10-03 16:41:04'),
(27, 506, 22, 21, 'Excluir Mensagem', '', 'Y', '2019-09-29 14:41:37', '2020-10-03 16:41:04'),
(28, 1, 0, 0, 'Empresa', 'ni ni-info', 'N', '2019-09-29 15:38:24', '2019-10-28 03:43:14'),
(36, 516, 15, 1, 'Excluir item do menu', NULL, 'Y', '2019-10-02 00:21:06', '2020-10-03 16:41:04'),
(37, 6, 33, 0, 'Planos', 'ni ni-diamond', 'N', '2019-10-26 01:09:44', '2020-10-03 17:12:52'),
(39, 2, 36, 0, 'Notícias', 'ni ni-book-open', 'N', '2019-10-27 22:37:55', '2020-10-03 16:41:04'),
(40, 3, 37, 0, 'Cadastrar Notícia', NULL, 'Y', '2019-10-27 22:38:25', '2020-10-03 16:42:01'),
(41, 4, 38, 0, 'Excluir Notícia', '', 'Y', '2019-10-27 22:38:40', '2020-10-03 16:42:01'),
(42, 7, 35, 0, 'Cadastrar planos', '', 'Y', '2019-10-27 23:17:47', '2020-10-03 17:12:52'),
(43, 11, 39, 0, 'Revendas', 'fal fa-warehouse', 'N', '2019-11-07 23:19:55', '2020-10-03 17:12:52'),
(44, 8, 42, 0, 'Regiões de anúncios', 'fal fa-map', 'N', '2019-11-11 00:31:42', '2020-10-03 17:12:52'),
(45, 9, 43, 0, 'Excluir regiao', NULL, 'Y', '2019-11-11 00:32:08', '2020-10-03 17:12:52'),
(46, 10, 44, 0, 'Cadastrar região', NULL, 'Y', '2019-11-11 00:32:27', '2020-10-03 17:12:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_modulo`
--

CREATE TABLE `sistema_modulo` (
  `id` int(11) NOT NULL,
  `ordem` int(11) DEFAULT 1,
  `modulo` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `sistema_modulo`
--

INSERT INTO `sistema_modulo` (`id`, `ordem`, `modulo`, `label`, `ativo`, `created_at`, `updated_at`) VALUES
(3, 37, 'sistema', 'Sistema', 'Y', '2019-09-28 17:13:11', '2020-10-01 02:57:40'),
(5, 35, 'contato', 'Contato', 'Y', '2019-09-28 17:17:21', '2020-10-01 02:57:40'),
(6, 34, 'empresa', 'Sobre a empresa', 'Y', '2019-09-29 15:36:28', '2020-10-01 02:57:40'),
(7, 33, 'plano', 'Planos', 'Y', '2019-10-26 01:05:56', '2020-10-01 02:57:40'),
(8, 32, 'noticia', 'Notícias', 'Y', '2019-10-27 22:36:24', '2020-10-01 02:57:40'),
(9, 31, 'revenda', 'Revendas', 'Y', '2019-11-07 23:14:30', '2020-10-01 02:57:40'),
(10, 30, 'regiao', 'Regiões', 'Y', '2019-11-11 00:29:07', '2020-10-01 02:57:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_permissoes`
--

CREATE TABLE `sistema_permissoes` (
  `id` bigint(6) NOT NULL,
  `ordem` int(11) DEFAULT NULL,
  `nome` varchar(120) DEFAULT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `sistema_permissoes`
--

INSERT INTO `sistema_permissoes` (`id`, `ordem`, `nome`, `slug`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 2, 'Administrador Master', 'administrador-master', 'Y', '2013-08-13 09:11:51', '2019-09-22 22:05:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_usuario`
--

CREATE TABLE `sistema_usuario` (
  `id` bigint(6) NOT NULL,
  `permissao` bigint(6) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(40) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `ativo` enum('Y','N') DEFAULT 'Y',
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `sistema_usuario`
--

INSERT INTO `sistema_usuario` (`id`, `permissao`, `nome`, `slug`, `email`, `senha`, `avatar`, `ativo`, `ultimo_login`, `created_at`, `updated_at`) VALUES
(1, 1, 'Thiago Fernando', 'thiago-fernando', 'thyagogoth@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '5fa68fecbf714.png', 'Y', '2020-11-10 17:37:08', '2013-08-13 09:09:55', '2020-11-10 20:37:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_usuario_recuperar_senha`
--

CREATE TABLE `sistema_usuario_recuperar_senha` (
  `id` bigint(6) NOT NULL,
  `token` char(64) NOT NULL,
  `email` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `system_config` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `site` enum('pt_BR','en_US','es_ES') DEFAULT 'pt_BR',
  `logotipo_topo` varchar(180) DEFAULT NULL,
  `logotipo_rodape` varchar(180) DEFAULT NULL,
  `nome_site` varchar(180) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(180) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `bairro` varchar(180) DEFAULT NULL,
  `cidade` varchar(180) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `telefone` varchar(180) DEFAULT NULL,
  `whatsapp` varchar(180) DEFAULT NULL,
  `token_instagram` varchar(180) DEFAULT NULL,
  `php_mailer_host` varchar(180) DEFAULT NULL,
  `php_mailer_username` varchar(180) DEFAULT NULL,
  `php_mailer_password` varchar(180) DEFAULT NULL,
  `email_recipiente_pedidos` varchar(180) DEFAULT NULL,
  `info_parcelas_sem_juros` int(11) DEFAULT NULL,
  `email_pagseguro` varchar(150) DEFAULT NULL,
  `token_pagseguro_sandbox` varchar(150) DEFAULT NULL,
  `token_pagseguro_production` varchar(150) DEFAULT NULL,
  `notification_url` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `system_config_redes_sociais` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `site` enum('pt_BR','en_US','es_ES') DEFAULT 'pt_BR',
  `item` varchar(180) DEFAULT NULL,
  `link` varchar(180) DEFAULT NULL,
  `icone` varchar(180) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `contato_assunto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assunto` (`assunto`),
  ADD KEY `email` (`email`);

--
-- Índices de tabela `contato_mensagem`
--
ALTER TABLE `contato_mensagem`
  ADD PRIMARY KEY (`id`,`assunto`),
  ADD KEY `fk_contato_mensagem_contato_assunto` (`assunto`),
  ADD KEY `nome` (`nome`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresa_imagem`
--
ALTER TABLE `empresa_imagem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_imagem_produto1` (`empresa`);

--
-- Índices de tabela `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_cadastro` (`created_at`);
ALTER TABLE `noticia` ADD FULLTEXT KEY `titulo` (`titulo`);

--
-- Índices de tabela `noticia_imagem`
--
ALTER TABLE `noticia_imagem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_img_evento_evento1` (`noticia`);

ALTER TABLE `sistema_acesso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sistema_acesso_sistema_usuario` (`sistema_usuario`);

--
-- Índices de tabela `sistema_action`
--
ALTER TABLE `sistema_action`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sistema_action_permissao`
--
ALTER TABLE `sistema_action_permissao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sistema_menu_sistema_tipo_permissao_sistema_tipo_permissao1` (`permissao`);

--
-- Índices de tabela `sistema_log`
--
ALTER TABLE `sistema_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sistema_update_log_sistema_usuario1` (`usuario`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `pk` (`pk`),
  ADD KEY `tabela` (`tabela`),
  ADD KEY `campo` (`campo`);

--
-- Índices de tabela `sistema_menu`
--
ALTER TABLE `sistema_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item` (`menu`);

--
-- Índices de tabela `sistema_modulo`
--
ALTER TABLE `sistema_modulo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sistema_usuario`
--
ALTER TABLE `sistema_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sistema_usuario_recuperar_senha`
--
ALTER TABLE `sistema_usuario_recuperar_senha`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Índices de tabela `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_config_redes_sociais`
--
ALTER TABLE `system_config_redes_sociais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item` (`item`);

ALTER TABLE `contato_assunto`
  MODIFY `id` bigint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `contato_mensagem`
--
ALTER TABLE `contato_mensagem`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `empresa`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `empresa_imagem`
--
ALTER TABLE `empresa_imagem`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `noticia`
--
ALTER TABLE `noticia`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3415;

--
-- AUTO_INCREMENT de tabela `noticia_imagem`
--
ALTER TABLE `noticia_imagem`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4166;

--
-- AUTO_INCREMENT de tabela `sistema_acesso`
--
ALTER TABLE `sistema_acesso`
  MODIFY `id` bigint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sistema_action`
--
ALTER TABLE `sistema_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `sistema_action_permissao`
--
ALTER TABLE `sistema_action_permissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1213;

--
-- AUTO_INCREMENT de tabela `sistema_log`
--
ALTER TABLE `sistema_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sistema_menu`
--
ALTER TABLE `sistema_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `sistema_modulo`
--
ALTER TABLE `sistema_modulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `sistema_usuario`
--
ALTER TABLE `sistema_usuario`
  MODIFY `id` bigint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `sistema_usuario_recuperar_senha`
--
ALTER TABLE `sistema_usuario_recuperar_senha`
  MODIFY `id` bigint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `system_config_redes_sociais`
--
ALTER TABLE `system_config_redes_sociais`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
