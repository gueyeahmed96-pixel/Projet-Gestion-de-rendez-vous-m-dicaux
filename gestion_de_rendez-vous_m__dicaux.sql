-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 26 août 2025 à 04:18
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
-- Base de données : `gestion_de_rendez-vous_médicaux`
--

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patients`
--

CREATE TABLE `patients` (
  `id_patient` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(25) DEFAULT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `derniere_activite` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `patients`
--

INSERT INTO `patients` (`id_patient`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `date_inscription`, `derniere_activite`) VALUES
(1, 'Gueye', 'Aissatou', 'gueyeaissatou@gmail.com', '$2y$10$DQT26PchhYKj/sd8M8C5zOz7xRyYYWpyZcPHKjngpEE29whzVyjTq', '779711543', '2025-08-25 16:06:34', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

CREATE TABLE `personnel` (
  `id_personnel` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_specialite_fk` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'medecin',
  `derniere_activite` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnel`
--

INSERT INTO `personnel` (`id_personnel`, `nom`, `prenom`, `email`, `mot_de_passe`, `id_specialite_fk`, `role`, `derniere_activite`) VALUES
(1, 'Gueye', 'Ousmane', 'ousmane.gueye@sunuclinique.sn', '$2y$10$RZUgHOSZFHmDjnym0U1fCOZdFx1B/x/7rpKlMwYYeOfPl3Grick5m', 1, 'medecin', NULL),
(2, 'Ndiaye', 'Moussa', 'moussa.ndiaye@sunuclinique.sn', '$2y$10$7v.wY2iK3gA7r.bS9tJ8oO.sPqXzE6i.ZfW6yR2bO8c.V4nKzG9a', 2, 'medecin', NULL),
(3, 'Diop', 'Fatou', 'fatou.diop@sunuclinique.sn', '$2y$10$7v.wY2iK3gA7r.bS9tJ8oO.sPqXzE6i.ZfW6yR2bO8c.V4nKzG9a', 3, 'medecin', NULL),
(4, 'Sow', 'Mariama', 'mariama.sow@sunuclinique.sn', '$2y$10$7v.wY2iK3gA7r.bS9tJ8oO.sPqXzE6i.ZfW6yR2bO8c.V4nKzG9a', 4, 'medecin', NULL),
(5, 'Fall', 'Aïssatou', 'aissatou.fall@sunuclinique.sn', '$2y$10$7v.wY2iK3gA7r.bS9tJ8oO.sPqXzE6i.ZfW6yR2bO8c.V4nKzG9a', 5, 'medecin', NULL),
(6, 'Ba', 'Cheikh', 'cheikh.ba@sunuclinique.sn', '$2y$10$7v.wY2iK3gA7r.bS9tJ8oO.sPqXzE6i.ZfW6yR2bO8c.V4nKzG9a', 6, 'medecin', NULL),
(7, 'Admin', 'Super', 'admin@sunuclinique.sn', '$2y$10$AUq43V2ZH7hL1s9EEJmAE.OFOeRx0JRxFLacasItVdrn8jlApwjFa', NULL, 'admin', '2025-08-26 04:15:22');

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id_rdv` int(11) NOT NULL,
  `date_heure_rdv` datetime NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'Confirmé',
  `id_patient_fk` int(11) NOT NULL,
  `id_personnel_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id_rdv`, `date_heure_rdv`, `statut`, `id_patient_fk`, `id_personnel_fk`) VALUES
(1, '2025-09-02 09:00:00', 'Confirmé', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `specialites`
--

CREATE TABLE `specialites` (
  `id_specialite` int(11) NOT NULL,
  `nom_specialite` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `specialites`
--

INSERT INTO `specialites` (`id_specialite`, `nom_specialite`) VALUES
(1, 'Médecine Générale'),
(2, 'Cardiologie'),
(3, 'Pédiatrie'),
(4, 'Dermatologie'),
(5, 'Gynécologie'),
(6, 'Ophtalmologie');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Index pour la table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id_patient`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Index pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id_personnel`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `fk_personnel_specialite_idx` (`id_specialite_fk`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id_rdv`),
  ADD KEY `fk_rdv_patient_idx` (`id_patient_fk`),
  ADD KEY `fk_rdv_personnel_idx` (`id_personnel_fk`);

--
-- Index pour la table `specialites`
--
ALTER TABLE `specialites`
  ADD PRIMARY KEY (`id_specialite`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `patients`
--
ALTER TABLE `patients`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id_personnel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `specialites`
--
ALTER TABLE `specialites`
  MODIFY `id_specialite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD CONSTRAINT `fk_personnel_specialite` FOREIGN KEY (`id_specialite_fk`) REFERENCES `specialites` (`id_specialite`);

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `fk_rdv_patient` FOREIGN KEY (`id_patient_fk`) REFERENCES `patients` (`id_patient`),
  ADD CONSTRAINT `fk_rdv_personnel` FOREIGN KEY (`id_personnel_fk`) REFERENCES `personnel` (`id_personnel`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
