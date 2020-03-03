<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200224104419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE creneau_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE diplome_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE etudiant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE filiere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE module_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prof_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rendu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE salle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE soutenance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, compte_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_880E0D76F2C56620 ON admin (compte_id)');
        $this->addSql('CREATE TABLE creneau (id INT NOT NULL, soutenance_id INT NOT NULL, capacite SMALLINT DEFAULT NULL, date DATE NOT NULL, duree SMALLINT NOT NULL, heure TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F9668B5FA59B3775 ON creneau (soutenance_id)');
        $this->addSql('CREATE TABLE creneau_etudiant (creneau_id INT NOT NULL, etudiant_id INT NOT NULL, PRIMARY KEY(creneau_id, etudiant_id))');
        $this->addSql('CREATE INDEX IDX_26FF41A67D0729A9 ON creneau_etudiant (creneau_id)');
        $this->addSql('CREATE INDEX IDX_26FF41A6DDEAB1A3 ON creneau_etudiant (etudiant_id)');
        $this->addSql('CREATE TABLE diplome (id INT NOT NULL, admin_id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB4C4D4E642B8210 ON diplome (admin_id)');
        $this->addSql('CREATE TABLE etudiant (id INT NOT NULL, compte_id INT NOT NULL, filiere_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_717E22E3F2C56620 ON etudiant (compte_id)');
        $this->addSql('CREATE INDEX IDX_717E22E3180AA129 ON etudiant (filiere_id)');
        $this->addSql('CREATE TABLE filiere (id INT NOT NULL, diplome_id INT NOT NULL, nom VARCHAR(255) NOT NULL, annee VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2ED05D9E26F859E2 ON filiere (diplome_id)');
        $this->addSql('CREATE TABLE filiere_prof (filiere_id INT NOT NULL, prof_id INT NOT NULL, PRIMARY KEY(filiere_id, prof_id))');
        $this->addSql('CREATE INDEX IDX_D74072B2180AA129 ON filiere_prof (filiere_id)');
        $this->addSql('CREATE INDEX IDX_D74072B2ABC1F7FE ON filiere_prof (prof_id)');
        $this->addSql('CREATE TABLE module (id INT NOT NULL, filiere_id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C242628180AA129 ON module (filiere_id)');
        $this->addSql('CREATE TABLE note (id INT NOT NULL, soutenance_id INT NOT NULL, prof_id INT NOT NULL, etudiant_id INT NOT NULL, note SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CFBDFA14A59B3775 ON note (soutenance_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14ABC1F7FE ON note (prof_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14DDEAB1A3 ON note (etudiant_id)');
        $this->addSql('CREATE TABLE prof (id INT NOT NULL, compte_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBA70BBF2C56620 ON prof (compte_id)');
        $this->addSql('CREATE TABLE rendu (id INT NOT NULL, etudiant_id INT NOT NULL, soutenance_id INT NOT NULL, note SMALLINT DEFAULT NULL, rendu VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2A7F8EB9DDEAB1A3 ON rendu (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_2A7F8EB9A59B3775 ON rendu (soutenance_id)');
        $this->addSql('CREATE TABLE salle (id INT NOT NULL, nom VARCHAR(255) NOT NULL, etage SMALLINT DEFAULT NULL, capacite SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE salle_creneau (salle_id INT NOT NULL, creneau_id INT NOT NULL, PRIMARY KEY(salle_id, creneau_id))');
        $this->addSql('CREATE INDEX IDX_D450E70EDC304035 ON salle_creneau (salle_id)');
        $this->addSql('CREATE INDEX IDX_D450E70E7D0729A9 ON salle_creneau (creneau_id)');
        $this->addSql('CREATE TABLE soutenance (id INT NOT NULL, module_id INT NOT NULL, prof_id INT NOT NULL, nom VARCHAR(255) NOT NULL, alerte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4D59FF6EAFC2B591 ON soutenance (module_id)');
        $this->addSql('CREATE INDEX IDX_4D59FF6EABC1F7FE ON soutenance (prof_id)');
        $this->addSql('CREATE TABLE soutenance_prof (soutenance_id INT NOT NULL, prof_id INT NOT NULL, PRIMARY KEY(soutenance_id, prof_id))');
        $this->addSql('CREATE INDEX IDX_D822EB0AA59B3775 ON soutenance_prof (soutenance_id)');
        $this->addSql('CREATE INDEX IDX_D822EB0AABC1F7FE ON soutenance_prof (prof_id)');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, mail_perso VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B389A1584E ON utilisateur (mail_perso)');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76F2C56620 FOREIGN KEY (compte_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5FA59B3775 FOREIGN KEY (soutenance_id) REFERENCES soutenance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE creneau_etudiant ADD CONSTRAINT FK_26FF41A67D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneau (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE creneau_etudiant ADD CONSTRAINT FK_26FF41A6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diplome ADD CONSTRAINT FK_EB4C4D4E642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3F2C56620 FOREIGN KEY (compte_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE filiere ADD CONSTRAINT FK_2ED05D9E26F859E2 FOREIGN KEY (diplome_id) REFERENCES diplome (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE filiere_prof ADD CONSTRAINT FK_D74072B2180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE filiere_prof ADD CONSTRAINT FK_D74072B2ABC1F7FE FOREIGN KEY (prof_id) REFERENCES prof (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A59B3775 FOREIGN KEY (soutenance_id) REFERENCES soutenance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14ABC1F7FE FOREIGN KEY (prof_id) REFERENCES prof (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prof ADD CONSTRAINT FK_5BBA70BBF2C56620 FOREIGN KEY (compte_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9A59B3775 FOREIGN KEY (soutenance_id) REFERENCES soutenance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salle_creneau ADD CONSTRAINT FK_D450E70EDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salle_creneau ADD CONSTRAINT FK_D450E70E7D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneau (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EABC1F7FE FOREIGN KEY (prof_id) REFERENCES prof (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE soutenance_prof ADD CONSTRAINT FK_D822EB0AA59B3775 FOREIGN KEY (soutenance_id) REFERENCES soutenance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE soutenance_prof ADD CONSTRAINT FK_D822EB0AABC1F7FE FOREIGN KEY (prof_id) REFERENCES prof (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE diplome DROP CONSTRAINT FK_EB4C4D4E642B8210');
        $this->addSql('ALTER TABLE creneau_etudiant DROP CONSTRAINT FK_26FF41A67D0729A9');
        $this->addSql('ALTER TABLE salle_creneau DROP CONSTRAINT FK_D450E70E7D0729A9');
        $this->addSql('ALTER TABLE filiere DROP CONSTRAINT FK_2ED05D9E26F859E2');
        $this->addSql('ALTER TABLE creneau_etudiant DROP CONSTRAINT FK_26FF41A6DDEAB1A3');
        $this->addSql('ALTER TABLE note DROP CONSTRAINT FK_CFBDFA14DDEAB1A3');
        $this->addSql('ALTER TABLE rendu DROP CONSTRAINT FK_2A7F8EB9DDEAB1A3');
        $this->addSql('ALTER TABLE etudiant DROP CONSTRAINT FK_717E22E3180AA129');
        $this->addSql('ALTER TABLE filiere_prof DROP CONSTRAINT FK_D74072B2180AA129');
        $this->addSql('ALTER TABLE module DROP CONSTRAINT FK_C242628180AA129');
        $this->addSql('ALTER TABLE soutenance DROP CONSTRAINT FK_4D59FF6EAFC2B591');
        $this->addSql('ALTER TABLE filiere_prof DROP CONSTRAINT FK_D74072B2ABC1F7FE');
        $this->addSql('ALTER TABLE note DROP CONSTRAINT FK_CFBDFA14ABC1F7FE');
        $this->addSql('ALTER TABLE soutenance DROP CONSTRAINT FK_4D59FF6EABC1F7FE');
        $this->addSql('ALTER TABLE soutenance_prof DROP CONSTRAINT FK_D822EB0AABC1F7FE');
        $this->addSql('ALTER TABLE salle_creneau DROP CONSTRAINT FK_D450E70EDC304035');
        $this->addSql('ALTER TABLE creneau DROP CONSTRAINT FK_F9668B5FA59B3775');
        $this->addSql('ALTER TABLE note DROP CONSTRAINT FK_CFBDFA14A59B3775');
        $this->addSql('ALTER TABLE rendu DROP CONSTRAINT FK_2A7F8EB9A59B3775');
        $this->addSql('ALTER TABLE soutenance_prof DROP CONSTRAINT FK_D822EB0AA59B3775');
        $this->addSql('ALTER TABLE admin DROP CONSTRAINT FK_880E0D76F2C56620');
        $this->addSql('ALTER TABLE etudiant DROP CONSTRAINT FK_717E22E3F2C56620');
        $this->addSql('ALTER TABLE prof DROP CONSTRAINT FK_5BBA70BBF2C56620');
        $this->addSql('DROP SEQUENCE admin_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE creneau_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE diplome_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE etudiant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE filiere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE module_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prof_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rendu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE salle_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE soutenance_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE creneau');
        $this->addSql('DROP TABLE creneau_etudiant');
        $this->addSql('DROP TABLE diplome');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE filiere_prof');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE prof');
        $this->addSql('DROP TABLE rendu');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE salle_creneau');
        $this->addSql('DROP TABLE soutenance');
        $this->addSql('DROP TABLE soutenance_prof');
        $this->addSql('DROP TABLE utilisateur');
    }
}
