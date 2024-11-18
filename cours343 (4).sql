-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 08 août 2024 à 19:59
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
-- Base de données : `cours343`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--
CREATE DATABASE cours343;
CREATE TABLE `adresse` (
  `id_adresse` int(11) NOT NULL,
  `rue` varchar(100) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `pays` varchar(50) DEFAULT 'Canada',
  `numero` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `adresse`
--

INSERT INTO `adresse` (`id_adresse`, `rue`, `ville`, `code_postal`, `pays`, `numero`, `province`) VALUES
(1, 'Saint-Laurent', 'Montreal', 'J5U2W0', 'Canada', '309', 'Quebec'),
(2, ' issac-christin', 'Repentigny', 'J5Z5C1', 'Canada', '220', 'Quebec');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom_categorie`) VALUES
(1, 'Boubou'),
(2, 'Pantalon'),
(3, 'T-shirt'),
(4, 'Chapeau'),
(5, 'Babouches');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_commande` date NOT NULL,
  `prix_total` varchar(10) NOT NULL,
  `statut` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_utilisateur`, `date_commande`, `prix_total`, `statut`) VALUES
(8, 1, '2024-07-22', '204', 'Annulee'),
(17, 3, '2024-07-25', '113', 'Annulee'),
(18, 3, '2024-07-25', '113', 'Annulee'),
(19, 3, '2024-07-25', '115', 'Annulee'),
(20, 4, '2024-07-25', '229', 'En attente'),
(21, 3, '2024-07-25', '60', 'Annulee'),
(22, 6, '2024-07-25', '292', 'Annulee'),
(23, 3, '2024-07-27', '146', 'En expedition'),
(24, 3, '2024-07-27', '180', 'En attente'),
(25, 3, '2024-07-27', '135', 'En traitement'),
(26, 3, '2024-07-27', '115', 'En attente'),
(27, 3, '2024-07-29', '180', 'Annulee'),
(28, 3, '2024-07-30', '220', 'En attente'),
(29, 6, '2024-08-08', '343.45', 'En attente'),
(30, 3, '2024-08-08', '60', 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE `image` (
  `id_image` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `chemin_image` text DEFAULT NULL,
  `nom_image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id_image`, `id_produit`, `chemin_image`, `nom_image`) VALUES
(14, 56, 'images/Image 010.jpeg', ''),
(15, 57, 'images/images (6).jpg', ''),
(16, 58, 'images/images (5).jpg', ''),
(17, 59, 'images/49.jpg', ''),
(18, 60, 'images/bf7e75f8488443a5a6c3b96452b3fc69.jpg', ''),
(19, 61, 'images/Blog-Atelier-Invitation-Instagram-publication-85.png', ''),
(20, 62, 'images/Image 006.jpeg', ''),
(21, 63, 'images/93a1743c5bed28289dbdff0114e3f61d.jpg', ''),
(22, 67, 'images/8712-b626hw-300x300.jpg', ''),
(24, 70, 'images/A4BF9S3X-large.jpg', ''),
(25, 71, 'images/il_570xN.2830509194_2fzf.jpg', ''),
(26, 72, 'images/66855df5b8b642a19a90f11de82f679e.jpg', '');

-- --------------------------------------------------------

--
-- Structure de la table `produitpromotion`
--

CREATE TABLE `produitpromotion` (
  `id_produit` int(11) NOT NULL,
  `id_promotion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produitpromotion`
--

INSERT INTO `produitpromotion` (`id_produit`, `id_promotion`) VALUES
(57, 9),
(60, 8),
(72, 10);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prix_unitaire` float NOT NULL,
  `description` text DEFAULT NULL,
  `courte_description` varchar(250) NOT NULL,
  `quantite` int(11) DEFAULT 0,
  `id_categorie` int(11) NOT NULL,
  `taille_produit` varchar(10) NOT NULL,
  `sexe_prod` varchar(10) DEFAULT NULL,
  `couleurs_prod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `prix_unitaire`, `description`, `courte_description`, `quantite`, `id_categorie`, `taille_produit`, `sexe_prod`, `couleurs_prod`) VALUES
(56, 't-shirt', 13, 'ererrerer', 'courte description edit', 34, 3, 'Large', NULL, NULL),
(57, 'Babouches L', 55, 'babouches locales fabriques au pays par des artisants locaux', 'babouches locales', 93, 5, 'Medium', NULL, NULL),
(58, 'Dallou-Ngaye', 60, 'sandale noirs  de très bonne qualité ', 'sandale noirs ', 99, 5, 'Medium', NULL, NULL),
(59, 'Babouches A', 57, 'babouches locales', 'babouches locales', 50, 5, 'Large', NULL, NULL),
(60, 'Thiarakh', 45, 'babouches localesbabouches localesbabouches localesbabouches localesbabouches locales', 'babouches locales', 94, 5, 'Large', NULL, NULL),
(61, 'Babouches Y', 40, '                        courte description courte description courte description courte description courte description                 ', '                        courte description                 ', 99, 5, 'XL', NULL, NULL),
(62, 't-shirt Co', 12, 'courte description courte description courte description courte description courte description courte description courte description ', 'courte description ', 450, 3, 'XXL', NULL, NULL),
(63, 'Co Af', 120, 'courte description courte description courte description courte description courte description courte description courte description ', 'courte description ', 89, 1, 'Large', NULL, NULL),
(67, 'Copati', 10, 'courte description courte description courte description ', 'vcourte description courte description ', 450, 4, 'Medium', NULL, NULL),
(70, 'Bonnet', 15, 'courte description ', 'courte description ', 90, 1, 'Large', NULL, NULL),
(71, 'Beret', 7, 'courte description courte description courte description courte description courte description ', 'courte description ', 100, 4, 'Large', NULL, NULL),
(72, 'Sandales', 25, 'courte description ', 'courte description ', 30, 5, 'Large', 'Femme', 'Rouge, Bleu, Noir');

-- --------------------------------------------------------

--
-- Structure de la table `produit_commande`
--

CREATE TABLE `produit_commande` (
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produit_commande`
--

INSERT INTO `produit_commande` (`id_commande`, `id_produit`, `quantite`) VALUES
(17, 56, 2),
(17, 59, 1),
(18, 56, 2),
(18, 59, 1),
(19, 56, 1),
(19, 59, 1),
(19, 60, 1),
(20, 56, 3),
(20, 58, 3),
(21, 58, 1),
(22, 56, 4),
(22, 58, 4),
(23, 56, 2),
(23, 58, 2),
(24, 58, 3),
(25, 60, 3),
(26, 56, 1),
(26, 59, 1),
(26, 60, 1),
(27, 58, 3),
(28, 57, 4),
(29, 57, 3),
(29, 60, 1),
(29, 61, 1),
(29, 63, 1),
(30, 58, 1);

-- --------------------------------------------------------

--
-- Structure de la table `promotions`
--

CREATE TABLE `promotions` (
  `id_promotion` int(11) NOT NULL,
  `code_promotion` varchar(50) DEFAULT NULL,
  `type` enum('pourcentage','montant') NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `promotions`
--

INSERT INTO `promotions` (`id_promotion`, `code_promotion`, `type`, `valeur`, `date_debut`, `date_fin`) VALUES
(1, 'QWERTY', 'pourcentage', 10.00, '2024-08-01', '2024-08-29'),
(3, NULL, 'pourcentage', 9.00, '2024-08-01', '2024-08-14'),
(4, NULL, 'pourcentage', 15.00, '2024-08-01', '2024-08-08'),
(5, NULL, 'pourcentage', 14.00, '2024-08-01', '2024-08-12'),
(6, NULL, 'pourcentage', 15.00, '2024-08-01', '2024-08-22'),
(7, NULL, 'pourcentage', 14.00, '2024-08-02', '2024-08-13'),
(8, NULL, 'pourcentage', 15.00, '2024-08-02', '2024-08-09'),
(9, NULL, 'pourcentage', 12.00, '2024-08-02', '2024-08-16'),
(10, NULL, 'pourcentage', 10.00, '2024-08-04', '2024-08-11');

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `description`) VALUES
(1, 'admin'),
(2, 'client');

-- --------------------------------------------------------

--
-- Structure de la table `role_utilisateur`
--

CREATE TABLE `role_utilisateur` (
  `id_role` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role_utilisateur`
--

INSERT INTO `role_utilisateur` (`id_role`, `id_utilisateur`) VALUES
(1, 3),
(2, 1),
(2, 4),
(2, 5),
(2, 6),
(2, 7);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `couriel` varchar(250) NOT NULL,
  `mot_de_pass` text NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `statut` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom_utilisateur`, `prenom`, `date_naissance`, `couriel`, `mot_de_pass`, `telephone`, `statut`) VALUES
(1, 'NDONG', 'Lamine Sene', '2007-04-03', 'ndong.lamine10gmail.com', '$2y$10$6eH5cQ.u/pAs4P3/czC0hONEReUvvZQ7C4n0.QZgC8JE4t/BrM6IK', '5147483630', 'actif'),
(3, 'GNING', 'Youssouf', '2013-07-07', 'gningyussuf@gmail.com', '$2y$10$fEh1MwjukD9GNXn7lH24aOhwNEyKtVd4ynUPTh3jTyY8I5Z7boF2S', '987654321', 'actif'),
(4, 'Paul', 'DIAGNE', '2000-03-05', 'paulstar@gmail.com', '$2y$10$96WZ.2vIJnbPwG9Xq0kjsusK7I9oN7LpZtEL9odGJsvTfnkRlcZoq', '5147483647', 'actif'),
(5, 'SARR', 'Jack', '1996-01-31', 'jacky.sarr123@gmail.com', '$2y$10$hxljZxwBazPvH.WEXlvJfOur/kkaY1oSRRzKwHorbDfay5cYJb.XS', '5147483630', 'actif'),
(6, 'SAMB', 'Ibou', '1986-03-03', 'boysambibou@gmail.com', '$2y$10$3e5Q0XBS3WfjGNcTnXyAy.JSkMosewuTIDtpzFxk.7wTJr2Gz0yVq', '5147483647', 'actif'),
(7, 'Youssou', 'GNING', '1983-01-01', 'themaestro@gmail.com', '$2y$10$vSdDiIU5wjCMYE.cvhVjmO7YJ/3alEO7Y6gOt/rnnWT0dTkZ/45L2', '5147483647', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_adresse`
--

CREATE TABLE `utilisateur_adresse` (
  `id_utilisateur` int(11) NOT NULL,
  `id_adresse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur_adresse`
--

INSERT INTO `utilisateur_adresse` (`id_utilisateur`, `id_adresse`) VALUES
(6, 1),
(7, 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`id_adresse`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id_image`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `produitpromotion`
--
ALTER TABLE `produitpromotion`
  ADD PRIMARY KEY (`id_produit`,`id_promotion`),
  ADD KEY `id_promotion` (`id_promotion`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `fk_p_c` (`id_categorie`);

--
-- Index pour la table `produit_commande`
--
ALTER TABLE `produit_commande`
  ADD PRIMARY KEY (`id_commande`,`id_produit`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id_promotion`),
  ADD UNIQUE KEY `code_promotion` (`code_promotion`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Index pour la table `role_utilisateur`
--
ALTER TABLE `role_utilisateur`
  ADD PRIMARY KEY (`id_role`,`id_utilisateur`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `couriel` (`couriel`);

--
-- Index pour la table `utilisateur_adresse`
--
ALTER TABLE `utilisateur_adresse`
  ADD PRIMARY KEY (`id_utilisateur`,`id_adresse`),
  ADD KEY `id_adresse` (`id_adresse`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `id_adresse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
  MODIFY `id_image` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT pour la table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id_promotion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produitpromotion`
--
ALTER TABLE `produitpromotion`
  ADD CONSTRAINT `produitpromotion_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `produitpromotion_ibfk_2` FOREIGN KEY (`id_promotion`) REFERENCES `promotions` (`id_promotion`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_p_c` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit_commande`
--
ALTER TABLE `produit_commande`
  ADD CONSTRAINT `produit_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `produit_commande_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `role_utilisateur`
--
ALTER TABLE `role_utilisateur`
  ADD CONSTRAINT `role_utilisateur_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_utilisateur_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur_adresse`
--
ALTER TABLE `utilisateur_adresse`
  ADD CONSTRAINT `utilisateur_adresse_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `utilisateur_adresse_ibfk_2` FOREIGN KEY (`id_adresse`) REFERENCES `adresse` (`id_adresse`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
