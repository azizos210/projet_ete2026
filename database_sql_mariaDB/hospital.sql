-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : dim. 21 juin 2026 à 09:11
-- Version du serveur : 11.4.9-MariaDB
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hospital`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `niveau_acces` varchar(30) NOT NULL DEFAULT 'standard',
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_32EB52E8FB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `administration_medicament`
--

DROP TABLE IF EXISTS `administration_medicament`;
CREATE TABLE IF NOT EXISTS `administration_medicament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure` datetime NOT NULL,
  `dose_administree` varchar(100) DEFAULT NULL,
  `observations` longtext DEFAULT NULL,
  `contre_indication_signalee` tinyint(1) NOT NULL DEFAULT 0,
  `ligne_prescription_id` int(11) NOT NULL,
  `infirmier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF42C4F983A202E` (`ligne_prescription_id`),
  KEY `IDX_BF42C4FC2BE0752` (`infirmier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL,
  `entite_cible` varchar(100) DEFAULT NULL,
  `entite_id` int(11) DEFAULT NULL,
  `adresse_ip` varchar(45) DEFAULT NULL,
  `date_action` datetime NOT NULL,
  `donnees_avant` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`donnees_avant`)),
  `donnees_apres` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`donnees_apres`)),
  `utilisateur_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F6E1C0F5FB88E14F` (`utilisateur_id`),
  KEY `idx_audit_date` (`date_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis_specialise`
--

DROP TABLE IF EXISTS `avis_specialise`;
CREATE TABLE IF NOT EXISTS `avis_specialise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` longtext NOT NULL,
  `reponse` longtext DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente',
  `date_demande` datetime NOT NULL,
  `date_reponse` datetime DEFAULT NULL,
  `medecin_demandeur_id` int(11) NOT NULL,
  `medecin_specialiste_id` int(11) NOT NULL,
  `dossier_medical_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4452CD3FBA4A54D` (`medecin_demandeur_id`),
  KEY `IDX_4452CD3FA0BA8D5F` (`medecin_specialiste_id`),
  KEY `IDX_4452CD3F7750B79F` (`dossier_medical_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consultation`
--

DROP TABLE IF EXISTS `consultation`;
CREATE TABLE IF NOT EXISTS `consultation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `symptomes` longtext DEFAULT NULL,
  `examen_clinique` longtext DEFAULT NULL,
  `diagnostic` longtext DEFAULT NULL,
  `recommandations` longtext DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_cours',
  `rendez_vous_id` int(11) DEFAULT NULL,
  `dossier_medical_id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  `infirmier_id` int(11) DEFAULT NULL,
  `validateur_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_964685A691EF7EAA` (`rendez_vous_id`),
  KEY `IDX_964685A67750B79F` (`dossier_medical_id`),
  KEY `IDX_964685A64F31A84` (`medecin_id`),
  KEY `IDX_964685A6C2BE0752` (`infirmier_id`),
  KEY `IDX_964685A6E57AEF2F` (`validateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `directeur_medical`
--

DROP TABLE IF EXISTS `directeur_medical`;
CREATE TABLE IF NOT EXISTS `directeur_medical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `specialite_supervision` varchar(150) DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_96EE22FCFB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `disponibilite_medecin`
--

DROP TABLE IF EXISTS `disponibilite_medecin`;
CREATE TABLE IF NOT EXISTS `disponibilite_medecin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jour_semaine` varchar(15) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `recurrent` tinyint(1) NOT NULL DEFAULT 1,
  `medecin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6C86D4814F31A84` (`medecin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260620011400', '2026-06-20 01:14:43', 3479);

-- --------------------------------------------------------

--
-- Structure de la table `document_medical`
--

DROP TABLE IF EXISTS `document_medical`;
CREATE TABLE IF NOT EXISTS `document_medical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `chemin_fichier` varchar(500) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `date_upload` datetime NOT NULL,
  `dossier_medical_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D3B4A1867750B79F` (`dossier_medical_id`),
  KEY `IDX_D3B4A18662FF6CDF` (`consultation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossier_medical`
--

DROP TABLE IF EXISTS `dossier_medical`;
CREATE TABLE IF NOT EXISTS `dossier_medical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_creation` datetime NOT NULL,
  `antecedents_medicaux` longtext DEFAULT NULL,
  `antecedents_familiaux` longtext DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3581EE626B899279` (`patient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

DROP TABLE IF EXISTS `evaluation`;
CREATE TABLE IF NOT EXISTS `evaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` int(11) NOT NULL,
  `commentaire` longtext DEFAULT NULL,
  `date_evaluation` datetime NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1323A57562FF6CDF` (`consultation_id`),
  KEY `IDX_1323A5756B899279` (`patient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut_paiement` varchar(20) NOT NULL DEFAULT 'en_attente',
  `date_emission` datetime NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `secretaire_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FE866410F55AE19E` (`numero`),
  UNIQUE KEY `UNIQ_FE86641062FF6CDF` (`consultation_id`),
  KEY `IDX_FE8664106B899279` (`patient_id`),
  KEY `IDX_FE866410A90F02B2` (`secretaire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `infirmier`
--

DROP TABLE IF EXISTS `infirmier`;
CREATE TABLE IF NOT EXISTS `infirmier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `service` varchar(100) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BFEC55B9FB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `information_assurance`
--

DROP TABLE IF EXISTS `information_assurance`;
CREATE TABLE IF NOT EXISTS `information_assurance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compagnie` varchar(150) NOT NULL,
  `numero_police` varchar(100) NOT NULL,
  `statut_remboursement` varchar(50) DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_86E42C2C6B899279` (`patient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_prescription`
--

DROP TABLE IF EXISTS `ligne_prescription`;
CREATE TABLE IF NOT EXISTS `ligne_prescription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dosage` varchar(100) DEFAULT NULL,
  `frequence` varchar(100) DEFAULT NULL,
  `duree_jours` int(11) DEFAULT NULL,
  `instructions` longtext DEFAULT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicament_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A761F81693DB413D` (`prescription_id`),
  KEY `IDX_A761F816AB0D61F7` (`medicament_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `medecin`
--

DROP TABLE IF EXISTS `medecin`;
CREATE TABLE IF NOT EXISTS `medecin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `specialite` varchar(150) NOT NULL,
  `numero_ordre` varchar(50) NOT NULL,
  `signature_numerique` longtext DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1BDA53C6DC26B9F4` (`numero_ordre`),
  UNIQUE KEY `UNIQ_1BDA53C6FB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `medicament`
--

DROP TABLE IF EXISTS `medicament`;
CREATE TABLE IF NOT EXISTS `medicament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `forme_pharmaceutique` varchar(100) DEFAULT NULL,
  `dosage_standard` varchar(100) DEFAULT NULL,
  `contre_indications` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contenu` longtext NOT NULL,
  `date_envoi` datetime NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307F10335F61` (`expediteur_id`),
  KEY `IDX_B6BD307FA4F84F6E` (`destinataire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_envoi` datetime NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `destinataire_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF5476CAA4F84F6E` (`destinataire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `montant` decimal(10,2) NOT NULL,
  `methode` varchar(20) NOT NULL,
  `date_transaction` datetime NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `facture_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B1DC7A1E7F2DEE08` (`facture_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_naissance` date NOT NULL,
  `genre` varchar(10) DEFAULT NULL,
  `groupe_sanguin` varchar(10) DEFAULT NULL,
  `allergies` longtext DEFAULT NULL,
  `contact_urgence` varchar(255) DEFAULT NULL,
  `numero_securite_sociale` varchar(50) DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1ADAD7EBFB88E14F` (`utilisateur_id`),
  UNIQUE KEY `UNIQ_1ADAD7EB31AD32FB` (`numero_securite_sociale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prescription`
--

DROP TABLE IF EXISTS `prescription`;
CREATE TABLE IF NOT EXISTS `prescription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_emission` datetime NOT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'active',
  `pdf_genere` tinyint(1) NOT NULL DEFAULT 0,
  `consultation_id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1FBFB8D962FF6CDF` (`consultation_id`),
  KEY `IDX_1FBFB8D94F31A84` (`medecin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `protocole_medical`
--

DROP TABLE IF EXISTS `protocole_medical`;
CREATE TABLE IF NOT EXISTS `protocole_medical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'brouillon',
  `directeur_medical_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1E15D9A23A9FC64D` (`directeur_medical_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

DROP TABLE IF EXISTS `rendez_vous`;
CREATE TABLE IF NOT EXISTS `rendez_vous` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure` datetime NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente',
  `motif` varchar(255) DEFAULT NULL,
  `rappel_envoye` tinyint(1) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL,
  `patient_id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  `secretaire_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_65E8AA0A6B899279` (`patient_id`),
  KEY `IDX_65E8AA0A4F31A84` (`medecin_id`),
  KEY `IDX_65E8AA0AA90F02B2` (`secretaire_id`),
  KEY `idx_rdv_date` (`date_heure`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ressource_educative`
--

DROP TABLE IF EXISTS `ressource_educative`;
CREATE TABLE IF NOT EXISTS `ressource_educative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` longtext NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ressource_educative_patient`
--

DROP TABLE IF EXISTS `ressource_educative_patient`;
CREATE TABLE IF NOT EXISTS `ressource_educative_patient` (
  `ressource_educative_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`ressource_educative_id`,`patient_id`),
  KEY `IDX_A4D0A5EF798CA08F` (`ressource_educative_id`),
  KEY `IDX_A4D0A5EF6B899279` (`patient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `secretaire_medicale`
--

DROP TABLE IF EXISTS `secretaire_medicale`;
CREATE TABLE IF NOT EXISTS `secretaire_medicale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poste_accueil` varchar(100) DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_A3A623BCFB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `signes_vitaux`
--

DROP TABLE IF EXISTS `signes_vitaux`;
CREATE TABLE IF NOT EXISTS `signes_vitaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tension_arterielle` varchar(20) DEFAULT NULL,
  `frequence_cardiaque` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `saturation_oxygene` decimal(5,2) DEFAULT NULL,
  `date_mesure` datetime NOT NULL,
  `alerte_declenchee` tinyint(1) NOT NULL DEFAULT 0,
  `consultation_id` int(11) NOT NULL,
  `infirmier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_241656E62FF6CDF` (`consultation_id`),
  KEY `IDX_241656EC2BE0752` (`infirmier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ticket_assistance`
--

DROP TABLE IF EXISTS `ticket_assistance`;
CREATE TABLE IF NOT EXISTS `ticket_assistance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sujet` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'ouvert',
  `priorite` varchar(20) NOT NULL DEFAULT 'normale',
  `date_creation` datetime NOT NULL,
  `date_resolution` datetime DEFAULT NULL,
  `demandeur_id` int(11) NOT NULL,
  `secretaire_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8DDC17A695A6EE59` (`demandeur_id`),
  KEY `IDX_8DDC17A6A90F02B2` (`secretaire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL,
  `derniere_connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
