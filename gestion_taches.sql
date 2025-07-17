-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 27 déc. 2024 à 03:37
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_taches`
--

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expediteur_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `destinataire_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contenu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_envoye` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `expediteur_id` (`expediteur_id`),
  KEY `destinataire_id` (`destinataire_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_debut` date DEFAULT NULL COMMENT 'Date de début de la tâche',
  `date_fin` date DEFAULT NULL COMMENT 'Date de fin de la tâche',
  `status` enum('proposee','envoyee','compiled','validee','terminee') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'proposee',
  `etat` enum('Non démarrée','En cours','Non terminée','Terminée') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Non démarrée',
  `assigned_to` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `duree` int DEFAULT NULL COMMENT 'Durée en jours',
  `periode_activite` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'T1, T2, T3, T4',
  `valeurs_cibles` json DEFAULT NULL COMMENT 'Valeurs par trimestre ou KPI',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notification_seen` tinyint(1) DEFAULT '0',
  `notification_seen_proposed` tinyint(1) DEFAULT '0',
  `notification_seen_completed` tinyint(1) DEFAULT '0',
  `notification_seen_validated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `date_debut`, `date_fin`, `status`, `etat`, `assigned_to`, `duree`, `periode_activite`, `valeurs_cibles`, `created_at`, `updated_at`, `notification_seen`, `notification_seen_proposed`, `notification_seen_completed`, `notification_seen_validated`) VALUES
(41, 'CRGP', 'reuinion', '2024-12-06', '2024-12-10', 'validee', 'En cours', 'Tous', NULL, NULL, NULL, '2024-12-04 06:36:21', '2024-12-13 10:34:38', 1, 0, 0, 1),
(44, 'logement', 'paiement', '2024-12-09', '2024-12-10', 'validee', 'Non terminée', 'IM 7777', NULL, NULL, NULL, '2024-12-04 15:30:04', '2024-12-14 06:31:51', 1, 1, 1, 1),
(47, 'Budget de la commune urbaine Ihosy', 'Prise en charge de la budget annuel de la commune urbaine Ihosy', '2024-12-09', '2024-12-10', 'validee', 'Non terminée', 'IM 0101', NULL, NULL, NULL, '2024-12-05 13:19:39', '2024-12-14 07:45:02', 0, 0, 0, 1),
(49, 'Grand netoyage', 'ménage interne et externe', '2024-12-13', '2024-12-13', 'validee', 'Terminée', 'IM 2121', NULL, NULL, NULL, '2024-12-10 08:08:47', '2024-12-14 08:16:58', 0, 1, 1, 1),
(51, 'bazry', 'miatsena', '2024-12-16', '2024-12-16', 'validee', 'Non terminée', 'IM 7777', NULL, NULL, NULL, '2024-12-14 04:41:36', '2024-12-20 15:39:45', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `IM` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('chef_service','coordonateur','employé') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `notification_seen_pending` tinyint(1) DEFAULT '0',
  `division` enum('Tous','BAAF','DEBRFM','DIVPE','FINANCES LOCALES et TUTELLE DES EPN','CIR') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Tous',
  `reset_token_hash` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`IM`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`IM`, `username`, `password`, `email`, `role`, `created_at`, `status`, `notification_seen_pending`, `division`, `reset_token_hash`, `reset_token_expires_at`) VALUES
('IM 0101', 'Tsiresy Andriamihanta', '$2y$10$dxNnNOmWZNaFHOjrDBEeRuJP8Ivd5W4YeQsy5PfCaY.T157QwUyyq', 'tsiresyandriamihanta@gmail.com', 'employé', '2024-12-12 07:00:57', 'active', 0, 'FINANCES LOCALES et TUTELLE DES EPN', NULL, NULL),
('IM 1111', 'Hasiniaina', '$2y$10$4zVUMzmkhUCk9bL.amyQOuLHt6aYDGcB/n5utpEk.ZOXzCCnYkHae', 'tognendrazafrancis@gmail.com', 'employé', '2024-12-12 08:23:57', 'active', 1, 'DIVPE', 'b99bfa297d50e812f15bc67fe3f9c73493ca828654b44c508bc674330825722c', '2024-12-21 07:09:05'),
('IM 123456', 'ARIDINA Hasiniaina', '$2y$10$WGgsaqBLMP2jYpVsTKvph.DyhepU39G28FF8K7J2r0xW4Uo7Gq5iW', 'aridina@gmail.com', 'employé', '2024-12-12 07:35:53', 'active', 1, 'DIVPE', NULL, NULL),
('IM 2121', 'ALIDIANA', '$2y$10$EAL.pG2pcZbqBa9BP5cIZO7ez/Als70GA8/ttHTrbo6GWaEyj7p8.', 'has.alidiana@gmail.com', 'coordonateur', '2024-12-04 17:04:09', 'active', 0, 'DEBRFM', NULL, NULL),
('IM 2222', 'Harena Angela', '$2y$10$f04sSDv8IWzn/AnmkaQlROqYNK0CGgOqU0yjjexJTvGZCOM/syIOm', 'harena@gmail.com', 'employé', '2024-12-12 08:26:09', 'active', 0, 'FINANCES LOCALES et TUTELLE DES EPN', NULL, NULL),
('IM 3333', 'aze', '$2y$10$iwG5pwx1zF81xZHdHp5sJedf1SKKvVRRMWbpmaxf9riDmEOn5uVwq', 'az@gmail.com', 'chef_service', '2024-12-04 06:19:31', 'active', 1, 'Tous', NULL, NULL),
('IM 4444', 'Anisah Zahra ', '$2y$10$e0NNwhsObOh0XOrNZGizYeU9dEnQwqmg.Q/a0GhUn0kLTLuJiSQNe', 'anisahzahra@gmail.com', 'employé', '2024-12-12 08:28:12', 'active', 0, 'FINANCES LOCALES et TUTELLE DES EPN', NULL, NULL),
('IM 5555', 'Sfidioa Gilbert', '$2y$10$Ky.4pJEuK.2jj9yO8RMF4uIeHFnGQqkNqltRwE3orqeyHi446qcQ.', 'safidisoagilberthasiniaina@gmail.com', 'employé', '2024-12-12 10:01:10', 'active', 0, 'BAAF', NULL, NULL),
('IM 7777', 'Anisah', '$2y$10$3QFI.4M5Ckhzoryd9Z1oBOVnILQc.OFuNBp2ozYsaqaLbX/efWTvG', 'anisah@gmail.com', 'employé', '2024-12-04 05:42:05', 'active', 0, 'DIVPE', NULL, NULL),
('IM 888888', 'Niavo', '$2y$10$27m8iwn9M5xOGTwuLrxOGuM.PJC2J4Gu9N3bxXDKMeYTvNQ3RvF8K', 'niavo@gmail.com', 'employé', '2024-12-13 10:25:22', 'active', 0, 'CIR', NULL, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`IM`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`IM`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
