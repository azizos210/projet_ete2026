<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260620011400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, niveau_acces VARCHAR(30) DEFAULT \'standard\' NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_32EB52E8FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE administration_medicament (id INT AUTO_INCREMENT NOT NULL, date_heure DATETIME NOT NULL, dose_administree VARCHAR(100) DEFAULT NULL, observations LONGTEXT DEFAULT NULL, contre_indication_signalee TINYINT(1) DEFAULT 0 NOT NULL, ligne_prescription_id INT NOT NULL, infirmier_id INT NOT NULL, INDEX IDX_BF42C4F983A202E (ligne_prescription_id), INDEX IDX_BF42C4FC2BE0752 (infirmier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(100) NOT NULL, entite_cible VARCHAR(100) DEFAULT NULL, entite_id INT DEFAULT NULL, adresse_ip VARCHAR(45) DEFAULT NULL, date_action DATETIME NOT NULL, donnees_avant JSON DEFAULT NULL, donnees_apres JSON DEFAULT NULL, utilisateur_id INT DEFAULT NULL, INDEX IDX_F6E1C0F5FB88E14F (utilisateur_id), INDEX idx_audit_date (date_action), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE avis_specialise (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, reponse LONGTEXT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, date_demande DATETIME NOT NULL, date_reponse DATETIME DEFAULT NULL, medecin_demandeur_id INT NOT NULL, medecin_specialiste_id INT NOT NULL, dossier_medical_id INT NOT NULL, INDEX IDX_4452CD3FBA4A54D (medecin_demandeur_id), INDEX IDX_4452CD3FA0BA8D5F (medecin_specialiste_id), INDEX IDX_4452CD3F7750B79F (dossier_medical_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, symptomes LONGTEXT DEFAULT NULL, examen_clinique LONGTEXT DEFAULT NULL, diagnostic LONGTEXT DEFAULT NULL, recommandations LONGTEXT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_cours\' NOT NULL, rendez_vous_id INT DEFAULT NULL, dossier_medical_id INT NOT NULL, medecin_id INT NOT NULL, infirmier_id INT DEFAULT NULL, validateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_964685A691EF7EAA (rendez_vous_id), INDEX IDX_964685A67750B79F (dossier_medical_id), INDEX IDX_964685A64F31A84 (medecin_id), INDEX IDX_964685A6C2BE0752 (infirmier_id), INDEX IDX_964685A6E57AEF2F (validateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE directeur_medical (id INT AUTO_INCREMENT NOT NULL, specialite_supervision VARCHAR(150) DEFAULT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_96EE22FCFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE disponibilite_medecin (id INT AUTO_INCREMENT NOT NULL, jour_semaine VARCHAR(15) NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, recurrent TINYINT(1) DEFAULT 1 NOT NULL, medecin_id INT NOT NULL, INDEX IDX_6C86D4814F31A84 (medecin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE document_medical (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, chemin_fichier VARCHAR(500) NOT NULL, titre VARCHAR(255) DEFAULT NULL, date_upload DATETIME NOT NULL, dossier_medical_id INT NOT NULL, consultation_id INT DEFAULT NULL, INDEX IDX_D3B4A1867750B79F (dossier_medical_id), INDEX IDX_D3B4A18662FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dossier_medical (id INT AUTO_INCREMENT NOT NULL, date_creation DATETIME NOT NULL, antecedents_medicaux LONGTEXT DEFAULT NULL, antecedents_familiaux LONGTEXT DEFAULT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_3581EE626B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_evaluation DATETIME NOT NULL, consultation_id INT NOT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_1323A57562FF6CDF (consultation_id), INDEX IDX_1323A5756B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(50) NOT NULL, montant NUMERIC(10, 2) NOT NULL, statut_paiement VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, date_emission DATETIME NOT NULL, consultation_id INT NOT NULL, patient_id INT NOT NULL, secretaire_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_FE866410F55AE19E (numero), UNIQUE INDEX UNIQ_FE86641062FF6CDF (consultation_id), INDEX IDX_FE8664106B899279 (patient_id), INDEX IDX_FE866410A90F02B2 (secretaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE infirmier (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(50) NOT NULL, service VARCHAR(100) NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_BFEC55B9FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE information_assurance (id INT AUTO_INCREMENT NOT NULL, compagnie VARCHAR(150) NOT NULL, numero_police VARCHAR(100) NOT NULL, statut_remboursement VARCHAR(50) DEFAULT NULL, date_expiration DATE DEFAULT NULL, patient_id INT NOT NULL, INDEX IDX_86E42C2C6B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_prescription (id INT AUTO_INCREMENT NOT NULL, dosage VARCHAR(100) DEFAULT NULL, frequence VARCHAR(100) DEFAULT NULL, duree_jours INT DEFAULT NULL, instructions LONGTEXT DEFAULT NULL, prescription_id INT NOT NULL, medicament_id INT NOT NULL, INDEX IDX_A761F81693DB413D (prescription_id), INDEX IDX_A761F816AB0D61F7 (medicament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medecin (id INT AUTO_INCREMENT NOT NULL, specialite VARCHAR(150) NOT NULL, numero_ordre VARCHAR(50) NOT NULL, signature_numerique LONGTEXT DEFAULT NULL, actif TINYINT(1) DEFAULT 1 NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_1BDA53C6DC26B9F4 (numero_ordre), UNIQUE INDEX UNIQ_1BDA53C6FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medicament (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, forme_pharmaceutique VARCHAR(100) DEFAULT NULL, dosage_standard VARCHAR(100) DEFAULT NULL, contre_indications LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, lu TINYINT(1) DEFAULT 0 NOT NULL, expediteur_id INT NOT NULL, destinataire_id INT NOT NULL, INDEX IDX_B6BD307F10335F61 (expediteur_id), INDEX IDX_B6BD307FA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, contenu LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, lu TINYINT(1) DEFAULT 0 NOT NULL, destinataire_id INT NOT NULL, INDEX IDX_BF5476CAA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, montant NUMERIC(10, 2) NOT NULL, methode VARCHAR(20) NOT NULL, date_transaction DATETIME NOT NULL, reference VARCHAR(100) DEFAULT NULL, facture_id INT NOT NULL, INDEX IDX_B1DC7A1E7F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, date_naissance DATE NOT NULL, genre VARCHAR(10) DEFAULT NULL, groupe_sanguin VARCHAR(10) DEFAULT NULL, allergies LONGTEXT DEFAULT NULL, contact_urgence VARCHAR(255) DEFAULT NULL, numero_securite_sociale VARCHAR(50) DEFAULT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EB31AD32FB (numero_securite_sociale), UNIQUE INDEX UNIQ_1ADAD7EBFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, date_emission DATETIME NOT NULL, statut VARCHAR(30) DEFAULT \'active\' NOT NULL, pdf_genere TINYINT(1) DEFAULT 0 NOT NULL, consultation_id INT NOT NULL, medecin_id INT NOT NULL, INDEX IDX_1FBFB8D962FF6CDF (consultation_id), INDEX IDX_1FBFB8D94F31A84 (medecin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE protocole_medical (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, version VARCHAR(20) DEFAULT NULL, date_creation DATETIME NOT NULL, statut VARCHAR(20) DEFAULT \'brouillon\' NOT NULL, directeur_medical_id INT NOT NULL, INDEX IDX_1E15D9A23A9FC64D (directeur_medical_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, date_heure DATETIME NOT NULL, statut VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, motif VARCHAR(255) DEFAULT NULL, rappel_envoye TINYINT(1) DEFAULT 0 NOT NULL, date_creation DATETIME NOT NULL, patient_id INT NOT NULL, medecin_id INT NOT NULL, secretaire_id INT DEFAULT NULL, INDEX IDX_65E8AA0A6B899279 (patient_id), INDEX IDX_65E8AA0A4F31A84 (medecin_id), INDEX IDX_65E8AA0AA90F02B2 (secretaire_id), INDEX idx_rdv_date (date_heure), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource_educative (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, categorie VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource_educative_patient (ressource_educative_id INT NOT NULL, patient_id INT NOT NULL, INDEX IDX_A4D0A5EF798CA08F (ressource_educative_id), INDEX IDX_A4D0A5EF6B899279 (patient_id), PRIMARY KEY(ressource_educative_id, patient_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE secretaire_medicale (id INT AUTO_INCREMENT NOT NULL, poste_accueil VARCHAR(100) DEFAULT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_A3A623BCFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE signes_vitaux (id INT AUTO_INCREMENT NOT NULL, tension_arterielle VARCHAR(20) DEFAULT NULL, frequence_cardiaque INT DEFAULT NULL, temperature NUMERIC(4, 1) DEFAULT NULL, saturation_oxygene NUMERIC(5, 2) DEFAULT NULL, date_mesure DATETIME NOT NULL, alerte_declenchee TINYINT(1) DEFAULT 0 NOT NULL, consultation_id INT NOT NULL, infirmier_id INT NOT NULL, INDEX IDX_241656E62FF6CDF (consultation_id), INDEX IDX_241656EC2BE0752 (infirmier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket_assistance (id INT AUTO_INCREMENT NOT NULL, sujet VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, statut VARCHAR(20) DEFAULT \'ouvert\' NOT NULL, priorite VARCHAR(20) DEFAULT \'normale\' NOT NULL, date_creation DATETIME NOT NULL, date_resolution DATETIME DEFAULT NULL, demandeur_id INT NOT NULL, secretaire_id INT DEFAULT NULL, INDEX IDX_8DDC17A695A6EE59 (demandeur_id), INDEX IDX_8DDC17A6A90F02B2 (secretaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, roles JSON NOT NULL, actif TINYINT(1) DEFAULT 1 NOT NULL, date_creation DATETIME NOT NULL, derniere_connexion DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE administration_medicament ADD CONSTRAINT FK_BF42C4F983A202E FOREIGN KEY (ligne_prescription_id) REFERENCES ligne_prescription (id)');
        $this->addSql('ALTER TABLE administration_medicament ADD CONSTRAINT FK_BF42C4FC2BE0752 FOREIGN KEY (infirmier_id) REFERENCES infirmier (id)');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F5FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE avis_specialise ADD CONSTRAINT FK_4452CD3FBA4A54D FOREIGN KEY (medecin_demandeur_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE avis_specialise ADD CONSTRAINT FK_4452CD3FA0BA8D5F FOREIGN KEY (medecin_specialiste_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE avis_specialise ADD CONSTRAINT FK_4452CD3F7750B79F FOREIGN KEY (dossier_medical_id) REFERENCES dossier_medical (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A691EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A67750B79F FOREIGN KEY (dossier_medical_id) REFERENCES dossier_medical (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A64F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6C2BE0752 FOREIGN KEY (infirmier_id) REFERENCES infirmier (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6E57AEF2F FOREIGN KEY (validateur_id) REFERENCES directeur_medical (id)');
        $this->addSql('ALTER TABLE directeur_medical ADD CONSTRAINT FK_96EE22FCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE disponibilite_medecin ADD CONSTRAINT FK_6C86D4814F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE document_medical ADD CONSTRAINT FK_D3B4A1867750B79F FOREIGN KEY (dossier_medical_id) REFERENCES dossier_medical (id)');
        $this->addSql('ALTER TABLE document_medical ADD CONSTRAINT FK_D3B4A18662FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE dossier_medical ADD CONSTRAINT FK_3581EE626B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57562FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5756B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641062FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664106B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410A90F02B2 FOREIGN KEY (secretaire_id) REFERENCES secretaire_medicale (id)');
        $this->addSql('ALTER TABLE infirmier ADD CONSTRAINT FK_BFEC55B9FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE information_assurance ADD CONSTRAINT FK_86E42C2C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE ligne_prescription ADD CONSTRAINT FK_A761F81693DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE ligne_prescription ADD CONSTRAINT FK_A761F816AB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
        $this->addSql('ALTER TABLE medecin ADD CONSTRAINT FK_1BDA53C6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D962FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D94F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE protocole_medical ADD CONSTRAINT FK_1E15D9A23A9FC64D FOREIGN KEY (directeur_medical_id) REFERENCES directeur_medical (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A4F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AA90F02B2 FOREIGN KEY (secretaire_id) REFERENCES secretaire_medicale (id)');
        $this->addSql('ALTER TABLE ressource_educative_patient ADD CONSTRAINT FK_A4D0A5EF798CA08F FOREIGN KEY (ressource_educative_id) REFERENCES ressource_educative (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource_educative_patient ADD CONSTRAINT FK_A4D0A5EF6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secretaire_medicale ADD CONSTRAINT FK_A3A623BCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE signes_vitaux ADD CONSTRAINT FK_241656E62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE signes_vitaux ADD CONSTRAINT FK_241656EC2BE0752 FOREIGN KEY (infirmier_id) REFERENCES infirmier (id)');
        $this->addSql('ALTER TABLE ticket_assistance ADD CONSTRAINT FK_8DDC17A695A6EE59 FOREIGN KEY (demandeur_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE ticket_assistance ADD CONSTRAINT FK_8DDC17A6A90F02B2 FOREIGN KEY (secretaire_id) REFERENCES secretaire_medicale (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8FB88E14F');
        $this->addSql('ALTER TABLE administration_medicament DROP FOREIGN KEY FK_BF42C4F983A202E');
        $this->addSql('ALTER TABLE administration_medicament DROP FOREIGN KEY FK_BF42C4FC2BE0752');
        $this->addSql('ALTER TABLE audit_log DROP FOREIGN KEY FK_F6E1C0F5FB88E14F');
        $this->addSql('ALTER TABLE avis_specialise DROP FOREIGN KEY FK_4452CD3FBA4A54D');
        $this->addSql('ALTER TABLE avis_specialise DROP FOREIGN KEY FK_4452CD3FA0BA8D5F');
        $this->addSql('ALTER TABLE avis_specialise DROP FOREIGN KEY FK_4452CD3F7750B79F');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A691EF7EAA');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A67750B79F');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A64F31A84');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6C2BE0752');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6E57AEF2F');
        $this->addSql('ALTER TABLE directeur_medical DROP FOREIGN KEY FK_96EE22FCFB88E14F');
        $this->addSql('ALTER TABLE disponibilite_medecin DROP FOREIGN KEY FK_6C86D4814F31A84');
        $this->addSql('ALTER TABLE document_medical DROP FOREIGN KEY FK_D3B4A1867750B79F');
        $this->addSql('ALTER TABLE document_medical DROP FOREIGN KEY FK_D3B4A18662FF6CDF');
        $this->addSql('ALTER TABLE dossier_medical DROP FOREIGN KEY FK_3581EE626B899279');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A57562FF6CDF');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5756B899279');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641062FF6CDF');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664106B899279');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410A90F02B2');
        $this->addSql('ALTER TABLE infirmier DROP FOREIGN KEY FK_BFEC55B9FB88E14F');
        $this->addSql('ALTER TABLE information_assurance DROP FOREIGN KEY FK_86E42C2C6B899279');
        $this->addSql('ALTER TABLE ligne_prescription DROP FOREIGN KEY FK_A761F81693DB413D');
        $this->addSql('ALTER TABLE ligne_prescription DROP FOREIGN KEY FK_A761F816AB0D61F7');
        $this->addSql('ALTER TABLE medecin DROP FOREIGN KEY FK_1BDA53C6FB88E14F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA4F84F6E');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E7F2DEE08');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBFB88E14F');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D962FF6CDF');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D94F31A84');
        $this->addSql('ALTER TABLE protocole_medical DROP FOREIGN KEY FK_1E15D9A23A9FC64D');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A6B899279');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A4F31A84');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AA90F02B2');
        $this->addSql('ALTER TABLE ressource_educative_patient DROP FOREIGN KEY FK_A4D0A5EF798CA08F');
        $this->addSql('ALTER TABLE ressource_educative_patient DROP FOREIGN KEY FK_A4D0A5EF6B899279');
        $this->addSql('ALTER TABLE secretaire_medicale DROP FOREIGN KEY FK_A3A623BCFB88E14F');
        $this->addSql('ALTER TABLE signes_vitaux DROP FOREIGN KEY FK_241656E62FF6CDF');
        $this->addSql('ALTER TABLE signes_vitaux DROP FOREIGN KEY FK_241656EC2BE0752');
        $this->addSql('ALTER TABLE ticket_assistance DROP FOREIGN KEY FK_8DDC17A695A6EE59');
        $this->addSql('ALTER TABLE ticket_assistance DROP FOREIGN KEY FK_8DDC17A6A90F02B2');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE administration_medicament');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE avis_specialise');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE directeur_medical');
        $this->addSql('DROP TABLE disponibilite_medecin');
        $this->addSql('DROP TABLE document_medical');
        $this->addSql('DROP TABLE dossier_medical');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE infirmier');
        $this->addSql('DROP TABLE information_assurance');
        $this->addSql('DROP TABLE ligne_prescription');
        $this->addSql('DROP TABLE medecin');
        $this->addSql('DROP TABLE medicament');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE protocole_medical');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('DROP TABLE ressource_educative');
        $this->addSql('DROP TABLE ressource_educative_patient');
        $this->addSql('DROP TABLE secretaire_medicale');
        $this->addSql('DROP TABLE signes_vitaux');
        $this->addSql('DROP TABLE ticket_assistance');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
