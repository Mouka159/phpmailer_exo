-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 02 mai 2026 à 03:10
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
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom`) VALUES
(1, 'Mode'),
(2, 'Maison'),
(3, 'Électronique');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `montant_total` decimal(10,2) NOT NULL,
  `statut` enum('En attente','Expédiée','Livrée') DEFAULT 'En attente',
  `adresse_livraison` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `utilisateur_id`, `date_commande`, `montant_total`, `statut`, `adresse_livraison`, `telephone`, `notes`, `created_at`) VALUES
(2, 26, '2026-05-01 23:56:07', 799.00, 'En attente', 'agoe, lomé 1245', '78451230', 'hello', '2026-05-01 23:56:07');

-- --------------------------------------------------------

--
-- Structure de la table `commande_produits`
--

CREATE TABLE `commande_produits` (
  `id_commande_produit` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_produits`
--

INSERT INTO `commande_produits` (`id_commande_produit`, `commande_id`, `produit_id`, `quantite`, `prix_unitaire`, `prix_total`) VALUES
(1, 2, 3, 1, 799.00, 799.00);

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id_panier` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id_panier`, `utilisateur_id`, `date_creation`) VALUES
(1, 21, '2026-04-18 08:22:09'),
(2, 22, '2026-04-26 22:29:25'),
(3, 23, '2026-04-28 20:42:43'),
(4, 26, '2026-05-01 23:41:37'),
(5, 27, '2026-05-02 00:39:30');

-- --------------------------------------------------------

--
-- Structure de la table `panier_produits`
--

CREATE TABLE `panier_produits` (
  `id_panier_produit` int(11) NOT NULL,
  `panier_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier_produits`
--

INSERT INTO `panier_produits` (`id_panier_produit`, `panier_id`, `produit_id`, `quantite`) VALUES
(1, 1, 5, 2),
(2, 1, 2, 3),
(5, 2, 5, 1),
(6, 2, 2, 5),
(7, 2, 1, 20);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `characteristics` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `characteristics`, `created_at`) VALUES
(1, 'casue', 'casque professionnel moins chere', 20000.00, 'brock-wegner-s1NPUzvw2-U-unsplash.jpg', 'couleur:rouge;poid:10kg;garantie:2ans;', '2026-04-17 18:54:14');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `description`, `prix`, `stock`, `image_url`, `id_categorie`) VALUES
(1, 'cap gucci', 'first modèle de la marque gucci', 74999.96, 20, '../image/produit_1776476235_cap.jpg', NULL),
(2, 'ADDIDAS', 'addidas tribande', 599.00, 10, '../image/produit_1776476149_addidas3bande.jpg', NULL),
(3, 'casque motard', 'casque pour la protection en route', 799.00, 15, '../image/produit_1776476083_casquemotartblack.jpg', NULL),
(5, 'PC DELL', 'pc dell 365giga 212ram ecran tactile processeurs de 25 generation', 50000.00, 2, '../image/produit_1776478153_dellpc.jpg', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('client','admin') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `telephone`, `email`, `mdp`, `otp`, `expire_at`, `verified`, `created_at`, `role`) VALUES
(9, 'bhs', 'fior', '12457896', 'fior@gmail.com', '$2y$10$LZvCcYnmOI0BKfoO0kw5leUNX4tUU3tJ7.FHMlBtl5N87ZfygBXW6', '998271', '2026-04-16 02:14:27', 0, '2026-04-16 02:13:27', 'client'),
(11, 'koffi', 'ama', '71769907', 'ama@gmail.com', '$2y$10$jgVgg09KFbaDcJWof1xCIOrMBJO3pUJmCbWloMtxoxd.zMrutike.', '802497', '2026-04-16 02:21:21', 0, '2026-04-16 02:20:21', 'client'),
(12, 'amavi', 'amapi', '71769907', 'amavi@gmail.com', '$2y$10$LpOmwOsex1JfhOHNjWdELOr78QKoHNFuLu16FqbYXF0i/edkWZXEi', '581854', '2026-04-16 02:24:12', 0, '2026-04-16 02:23:12', 'client'),
(20, 'adjo', 'adjovi', '96949782', 'manousetose@gmail.com', '$2y$10$n7BOcOl1zDeVS7o2eRuwL.jZd1T/eJDnCklb/p2DrS6oiIuYktR6e', '375752', '2026-04-17 02:34:26', 1, '2026-04-17 02:32:26', 'client'),
(25, 'SODOGA', 'Gloria', '70016984', 'Gloriasodoga15@gmail.com', '430893cdce2e7074821444975c1b6929f88957c6aa63f9e335673b61d241d1ef', NULL, NULL, 1, '2026-05-01 01:32:45', 'admin'),
(26, 'aicha', 'baicha', '78451230', 'tovoaicha1@gmail.com', '$2y$10$37vaGApUX.RcLiOmbu1cfu286b5NvxDn5x8KDFYFq09/a8al1MZ5q', '484166', '2026-05-02 00:42:04', 1, '2026-05-02 00:40:04', ''),
(27, 'TOVO', 'Moukaila', '71769907', 'moukailatovo9@gmail.com', '$2y$10$r6cvKQcYOKJsqxEuVRMRs.REhrJJDwfHO9EnuULl3UmT39hu0x1qG', '565729', '2026-05-02 02:40:05', 1, '2026-05-02 02:38:05', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `statut` (`statut`),
  ADD KEY `date_commande` (`date_commande`);

--
-- Index pour la table `commande_produits`
--
ALTER TABLE `commande_produits`
  ADD PRIMARY KEY (`id_commande_produit`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id_panier`);

--
-- Index pour la table `panier_produits`
--
ALTER TABLE `panier_produits`
  ADD PRIMARY KEY (`id_panier_produit`),
  ADD KEY `panier_id` (`panier_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `fk_produits_categories` (`id_categorie`);

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
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `commande_produits`
--
ALTER TABLE `commande_produits`
  MODIFY `id_commande_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `panier_produits`
--
ALTER TABLE `panier_produits`
  MODIFY `id_panier_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commande_produits`
--
ALTER TABLE `commande_produits`
  ADD CONSTRAINT `commande_produits_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_produits_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `panier_produits`
--
ALTER TABLE `panier_produits`
  ADD CONSTRAINT `panier_produits_ibfk_1` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id_panier`),
  ADD CONSTRAINT `panier_produits_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id_produit`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`),
  ADD CONSTRAINT `fk_produits_categories` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
