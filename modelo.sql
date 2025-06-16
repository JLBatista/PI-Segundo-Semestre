-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/05/2025 às 02:33
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `modelo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargahorariaprofessor`
--

CREATE TABLE `cargahorariaprofessor` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `dia_semana` enum('segunda','terca','quarta','quinta','sexta','sabado') NOT NULL,
  `aulas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `horariopreferencial_hae`
--

CREATE TABLE `horariopreferencial_hae` (
  `id` int(11) NOT NULL,
  `solicitacao_id` int(11) NOT NULL,
  `dia_semana` enum('segunda','terca','quarta','quinta','sexta','sabado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `parecer`
--

CREATE TABLE `parecer` (
  `id` int(11) NOT NULL,
  `solicitacao_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `data_avaliacao` date NOT NULL,
  `status_final` enum('aprovada','reprovada') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professor`
--

CREATE TABLE `professor` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `rg` varchar(20) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacao_hae`
--

CREATE TABLE `solicitacao_hae` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `data_envio` date NOT NULL,
  `carga_horaria` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `status` enum('aguardando','aprovada','reprovada','cancelada') DEFAULT 'aguardando'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_hae`
--

CREATE TABLE `tipo_hae` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `tipo_usuario` enum('administrador','professor','coordenacao','direcao') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `tipo_usuario`) VALUES
(3, 'Admin', 'admin@gmail', 'admin', 'administrador');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cargahorariaprofessor`
--
ALTER TABLE `cargahorariaprofessor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Índices de tabela `horariopreferencial_hae`
--
ALTER TABLE `horariopreferencial_hae`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitacao_id` (`solicitacao_id`);

--
-- Índices de tabela `parecer`
--
ALTER TABLE `parecer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitacao_id` (`solicitacao_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `solicitacao_hae`
--
ALTER TABLE `solicitacao_hae`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `tipo_id` (`tipo_id`);

--
-- Índices de tabela `tipo_hae`
--
ALTER TABLE `tipo_hae`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargahorariaprofessor`
--
ALTER TABLE `cargahorariaprofessor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `horariopreferencial_hae`
--
ALTER TABLE `horariopreferencial_hae`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `parecer`
--
ALTER TABLE `parecer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professor`
--
ALTER TABLE `professor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacao_hae`
--
ALTER TABLE `solicitacao_hae`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_hae`
--
ALTER TABLE `tipo_hae`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cargahorariaprofessor`
--
ALTER TABLE `cargahorariaprofessor`
  ADD CONSTRAINT `cargahorariaprofessor_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professor` (`id`);

--
-- Restrições para tabelas `horariopreferencial_hae`
--
ALTER TABLE `horariopreferencial_hae`
  ADD CONSTRAINT `horariopreferencial_hae_ibfk_1` FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacao_hae` (`id`);

--
-- Restrições para tabelas `parecer`
--
ALTER TABLE `parecer`
  ADD CONSTRAINT `parecer_ibfk_1` FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacao_hae` (`id`),
  ADD CONSTRAINT `parecer_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `professor`
--
ALTER TABLE `professor`
  ADD CONSTRAINT `fk_professor_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `solicitacao_hae`
--
ALTER TABLE `solicitacao_hae`
  ADD CONSTRAINT `solicitacao_hae_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professor` (`id`),
  ADD CONSTRAINT `solicitacao_hae_ibfk_2` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_hae` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
