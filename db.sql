
-- Table `specialites
CREATE TABLE `specialites` (
  `id_specialite` INT NOT NULL AUTO_INCREMENT,
  `nom_specialite` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_specialite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Table `patients
CREATE TABLE `patients` (
  `id_patient` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(25) DEFAULT NULL,
  `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id_patient`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Table `personnel
CREATE TABLE `personnel` (
  `id_personnel` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `id_specialite_fk` INT DEFAULT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'medecin',
  PRIMARY KEY (`id_personnel`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_personnel_specialite_idx` (`id_specialite_fk`),
  CONSTRAINT `fk_personnel_specialite` 
    FOREIGN KEY (`id_specialite_fk`) 
    REFERENCES `specialites` (`id_specialite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Table `rendez_vous
CREATE TABLE `rendez_vous` (
  `id_rdv` INT NOT NULL AUTO_INCREMENT,
  `date_heure_rdv` DATETIME NOT NULL,
  `statut` VARCHAR(50) NOT NULL DEFAULT 'Confirmé',
  `id_patient_fk` INT NOT NULL,
  `id_personnel_fk` INT NOT NULL,
  PRIMARY KEY (`id_rdv`),
  KEY `fk_rdv_patient_idx` (`id_patient_fk`),
  KEY `fk_rdv_personnel_idx` (`id_personnel_fk`),
  CONSTRAINT `fk_rdv_patient` 
    FOREIGN KEY (`id_patient_fk`) 
    REFERENCES `patients` (`id_patient`),
  CONSTRAINT `fk_rdv_personnel` 
    FOREIGN KEY (`id_personnel_fk`) 
    REFERENCES `personnel` (`id_personnel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Table `password_resets
CREATE TABLE `password_resets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `id_patient` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reset_patient_idx` (`id_patient`),
  CONSTRAINT `fk_reset_patient` 
    FOREIGN KEY (`id_patient`) 
    REFERENCES `patients` (`id_patient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;