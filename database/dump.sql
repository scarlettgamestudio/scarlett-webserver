-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tempo de Geração: 28/01/2017 às 15:09
-- Versão do servidor: 5.5.52-cll-lve
-- Versão do PHP: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `anlageme_scarlett_studio`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `id_project` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_project`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id_team` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_team`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `team_custom_role`
--

CREATE TABLE IF NOT EXISTS `team_custom_role` (
  `id_team_custom_role` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_team` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_team_custom_role`),
  KEY `id_team` (`id_team`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `team_project`
--

CREATE TABLE IF NOT EXISTS `team_project` (
  `id_project` int(11) unsigned NOT NULL,
  `id_team` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_project`,`id_team`),
  KEY `id_team` (`id_team`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `team_role`
--

CREATE TABLE IF NOT EXISTS `team_role` (
  `id_team_role` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_team_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '1',
  `register_date` datetime NOT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_ip` varchar(48) DEFAULT NULL,
  `display_name` varchar(32) DEFAULT NULL,
  `avatar_url` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_team`
--

CREATE TABLE IF NOT EXISTS `user_team` (
  `id_user` int(11) unsigned NOT NULL,
  `id_team` int(11) unsigned NOT NULL,
  `id_team_role` int(11) unsigned NOT NULL,
  `id_team_custom_role` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_user`,`id_team`),
  KEY `id_team` (`id_team`),
  KEY `id_team_role` (`id_team_role`),
  KEY `id_team_custom_role` (`id_team_custom_role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_token`
--

CREATE TABLE IF NOT EXISTS `user_token` (
  `id_user_token` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL,
  `token` varchar(48) NOT NULL DEFAULT '',
  `user_ip` varchar(48) NOT NULL DEFAULT '',
  `register_date` datetime DEFAULT NULL,
  `expiration_date` datetime NOT NULL,
  PRIMARY KEY (`id_user_token`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `team_custom_role`
--
ALTER TABLE `team_custom_role`
  ADD CONSTRAINT `team_custom_role_ibfk_1` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `team_project`
--
ALTER TABLE `team_project`
  ADD CONSTRAINT `team_project_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `team_project_ibfk_2` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `user_team`
--
ALTER TABLE `user_team`
  ADD CONSTRAINT `user_team_ibfk_1` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_team_ibfk_2` FOREIGN KEY (`id_team_role`) REFERENCES `team_role` (`id_team_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_team_ibfk_3` FOREIGN KEY (`id_team_custom_role`) REFERENCES `team_custom_role` (`id_team_custom_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `user_token`
--
ALTER TABLE `user_token`
  ADD CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
