-- Script pour ajouter l'adresse à la table utilisateur (si elle n'existe pas déjà)
ALTER TABLE `utilisateur` ADD COLUMN `adresse` varchar(255) DEFAULT NULL;

-- Ajouter la colonne role à la table utilisateur
ALTER TABLE `utilisateur` ADD COLUMN `role` enum('user', 'admin') DEFAULT 'user';

-- Créer la table Commandes si elle n'existe pas
CREATE TABLE IF NOT EXISTS `Commandes` (
  `id_commande` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `montant_total` decimal(10, 2) NOT NULL,
  `statut` enum('En attente', 'Expédiée', 'Livrée') DEFAULT 'En attente',
  `adresse_livraison` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_commande`),
  FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
  INDEX (`utilisateur_id`),
  INDEX (`statut`),
  INDEX (`date_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Créer la table Commande_Produits si elle n'existe pas
CREATE TABLE IF NOT EXISTS `Commande_Produits` (
  `id_commande_produit` int(11) NOT NULL AUTO_INCREMENT,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(10, 2) NOT NULL,
  `prix_total` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id_commande_produit`),
  FOREIGN KEY (`commande_id`) REFERENCES `Commandes`(`id_commande`) ON DELETE CASCADE,
  FOREIGN KEY (`produit_id`) REFERENCES `Produits`(`id_produit`) ON DELETE CASCADE,
  INDEX (`commande_id`),
  INDEX (`produit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
