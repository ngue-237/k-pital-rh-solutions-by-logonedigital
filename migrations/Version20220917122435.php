<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220917122435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, candidate_resume_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, languagewrite VARCHAR(50) NOT NULL, languagespeak VARCHAR(32) NOT NULL, INDEX IDX_D4DB71B5CD197225 (candidate_resume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5CD197225 FOREIGN KEY (candidate_resume_id) REFERENCES candidate_resume (id)');
        $this->addSql('ALTER TABLE candidature ADD candidate_resume_id INT DEFAULT NULL, ADD is_hired TINYINT(1) DEFAULT NULL, ADD expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD message LONGTEXT DEFAULT NULL, ADD cv VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B8CD197225 FOREIGN KEY (candidate_resume_id) REFERENCES candidate_resume (id)');
        $this->addSql('CREATE INDEX IDX_E33BD3B8CD197225 ON candidature (candidate_resume_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5CD197225');
        $this->addSql('DROP TABLE language');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B8CD197225');
        $this->addSql('DROP INDEX IDX_E33BD3B8CD197225 ON candidature');
        $this->addSql('ALTER TABLE candidature DROP candidate_resume_id, DROP is_hired, DROP expired_at, DROP message, DROP cv');
    }
}
