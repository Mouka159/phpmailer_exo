-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 18 avr. 2026 à 04:29
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `users`
--

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `expire_at` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `telephone`, `email`, `mdp`, `otp`, `expire_at`, `verified`, `created_at`) VALUES
(9, 'bhs', 'fior', '12457896', 'fior@gmail.com', '$2y$10$LZvCcYnmOI0BKfoO0kw5leUNX4tUU3tJ7.FHMlBtl5N87ZfygBXW6', '998271', '2026-04-16 02:14:27', 0, '2026-04-16 02:13:27'),
(11, 'koffi', 'ama', '71769907', 'ama@gmail.com', '$2y$10$jgVgg09KFbaDcJWof1xCIOrMBJO3pUJmCbWloMtxoxd.zMrutike.', '802497', '2026-04-16 02:21:21', 0, '2026-04-16 02:20:21'),
(12, 'amavi', 'amapi', '71769907', 'amavi@gmail.com', '$2y$10$LpOmwOsex1JfhOHNjWdELOr78QKoHNFuLu16FqbYXF0i/edkWZXEi', '581854', '2026-04-16 02:24:12', 0, '2026-04-16 02:23:12'),
(20, 'adjo', 'adjovi', '96949782', 'manousetose@gmail.com', '$2y$10$n7BOcOl1zDeVS7o2eRuwL.jZd1T/eJDnCklb/p2DrS6oiIuYktR6e', '375752', '2026-04-17 02:34:26', 1, '2026-04-17 02:32:26');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
