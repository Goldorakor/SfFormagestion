<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220023741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apprenant (id INT AUTO_INCREMENT NOT NULL, societe_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe VARCHAR(20) NOT NULL, adresse VARCHAR(100) NOT NULL, code_postal VARCHAR(20) NOT NULL, ville VARCHAR(50) NOT NULL, pays VARCHAR(50) NOT NULL, tel VARCHAR(20) DEFAULT NULL, email VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, metier VARCHAR(50) NOT NULL, INDEX IDX_C4EB462EFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE encadrement (id INT AUTO_INCREMENT NOT NULL, formateur_id INT DEFAULT NULL, session_id INT DEFAULT NULL, type_referent VARCHAR(50) NOT NULL, INDEX IDX_BF024B09155D8F51 (formateur_id), INDEX IDX_BF024B09613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, representant_id INT NOT NULL, raison_sociale VARCHAR(50) NOT NULL, statut_juri VARCHAR(20) NOT NULL, adresse_siege VARCHAR(100) NOT NULL, code_postal_siege VARCHAR(20) NOT NULL, ville_siege VARCHAR(50) NOT NULL, pays_siege VARCHAR(50) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, num_siret VARCHAR(20) NOT NULL, num_rcs VARCHAR(30) NOT NULL, num_tva VARCHAR(30) NOT NULL, num_decla_activite VARCHAR(30) NOT NULL, prefecture_region VARCHAR(30) NOT NULL, tribunal VARCHAR(100) NOT NULL, logo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D19FA606C4A52F0 (representant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formateur (id INT AUTO_INCREMENT NOT NULL, societe_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe VARCHAR(20) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, statut VARCHAR(20) NOT NULL, INDEX IDX_ED767E4FFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, programme_id INT NOT NULL, nom_formation VARCHAR(255) NOT NULL, modalites VARCHAR(50) NOT NULL, type VARCHAR(20) NOT NULL, date_maj DATE NOT NULL, INDEX IDX_404021BF62BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT DEFAULT NULL, session_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_5E90F6D6C5697D6D (apprenant_id), INDEX IDX_5E90F6D6613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom_module VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_C242628BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, module_id INT DEFAULT NULL, duree INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_FFC02E1B613FECDF (session_id), INDEX IDX_FFC02E1BAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programme (id INT AUTO_INCREMENT NOT NULL, nom_programme VARCHAR(255) NOT NULL, ref_programme VARCHAR(20) NOT NULL, prerequis LONGTEXT NOT NULL, objectifs_peda LONGTEXT NOT NULL, contenus_peda LONGTEXT NOT NULL, aptitude LONGTEXT NOT NULL, competences LONGTEXT NOT NULL, delai_acces LONGTEXT NOT NULL, moyens_enca LONGTEXT NOT NULL, methodes LONGTEXT NOT NULL, moyens_tech LONGTEXT NOT NULL, niveau LONGTEXT NOT NULL, modalites_acces LONGTEXT NOT NULL, modalites_eval LONGTEXT NOT NULL, accessibilite LONGTEXT NOT NULL, taux_reussite DOUBLE PRECISION NOT NULL, debouches LONGTEXT NOT NULL, cible LONGTEXT NOT NULL, date_maj DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questionnaire (id INT AUTO_INCREMENT NOT NULL, url_stockage VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE representant (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe VARCHAR(20) NOT NULL, fonction VARCHAR(50) NOT NULL, tampon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsabilite (id INT AUTO_INCREMENT NOT NULL, responsable_id INT DEFAULT NULL, societe_id INT DEFAULT NULL, type_responsable VARCHAR(50) NOT NULL, INDEX IDX_4EA0982053C59D72 (responsable_id), INDEX IDX_4EA09820FCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsable (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe VARCHAR(20) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, fonction VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, formation_id INT DEFAULT NULL, titre_session VARCHAR(255) NOT NULL, accroche LONGTEXT DEFAULT NULL, nb_places INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_D044D5D45200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, raison_sociale VARCHAR(50) NOT NULL, statut_juri VARCHAR(20) NOT NULL, adresse_siege VARCHAR(100) NOT NULL, code_postal_siege VARCHAR(20) NOT NULL, ville_siege VARCHAR(50) NOT NULL, pays_siege VARCHAR(50) NOT NULL, adresse_fac VARCHAR(100) NOT NULL, code_postal_fac VARCHAR(20) NOT NULL, ville_fac VARCHAR(50) NOT NULL, pays_fac VARCHAR(50) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, num_siret VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sondage (id INT AUTO_INCREMENT NOT NULL, questionnaire_id INT DEFAULT NULL, session_id INT DEFAULT NULL, type_questionnaire VARCHAR(50) NOT NULL, INDEX IDX_7579C89FCE07E8FF (questionnaire_id), INDEX IDX_7579C89F613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apprenant ADD CONSTRAINT FK_C4EB462EFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE encadrement ADD CONSTRAINT FK_BF024B09155D8F51 FOREIGN KEY (formateur_id) REFERENCES formateur (id)');
        $this->addSql('ALTER TABLE encadrement ADD CONSTRAINT FK_BF024B09613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA606C4A52F0 FOREIGN KEY (representant_id) REFERENCES representant (id)');
        $this->addSql('ALTER TABLE formateur ADD CONSTRAINT FK_ED767E4FFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF62BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE planification ADD CONSTRAINT FK_FFC02E1B613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE planification ADD CONSTRAINT FK_FFC02E1BAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE responsabilite ADD CONSTRAINT FK_4EA0982053C59D72 FOREIGN KEY (responsable_id) REFERENCES responsable (id)');
        $this->addSql('ALTER TABLE responsabilite ADD CONSTRAINT FK_4EA09820FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D45200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE sondage ADD CONSTRAINT FK_7579C89FCE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE sondage ADD CONSTRAINT FK_7579C89F613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant DROP FOREIGN KEY FK_C4EB462EFCF77503');
        $this->addSql('ALTER TABLE encadrement DROP FOREIGN KEY FK_BF024B09155D8F51');
        $this->addSql('ALTER TABLE encadrement DROP FOREIGN KEY FK_BF024B09613FECDF');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA606C4A52F0');
        $this->addSql('ALTER TABLE formateur DROP FOREIGN KEY FK_ED767E4FFCF77503');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF62BB7AEE');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6C5697D6D');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6613FECDF');
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628BCF5E72D');
        $this->addSql('ALTER TABLE planification DROP FOREIGN KEY FK_FFC02E1B613FECDF');
        $this->addSql('ALTER TABLE planification DROP FOREIGN KEY FK_FFC02E1BAFC2B591');
        $this->addSql('ALTER TABLE responsabilite DROP FOREIGN KEY FK_4EA0982053C59D72');
        $this->addSql('ALTER TABLE responsabilite DROP FOREIGN KEY FK_4EA09820FCF77503');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D45200282E');
        $this->addSql('ALTER TABLE sondage DROP FOREIGN KEY FK_7579C89FCE07E8FF');
        $this->addSql('ALTER TABLE sondage DROP FOREIGN KEY FK_7579C89F613FECDF');
        $this->addSql('DROP TABLE apprenant');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE encadrement');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE formateur');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE planification');
        $this->addSql('DROP TABLE programme');
        $this->addSql('DROP TABLE questionnaire');
        $this->addSql('DROP TABLE representant');
        $this->addSql('DROP TABLE responsabilite');
        $this->addSql('DROP TABLE responsable');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE sondage');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
