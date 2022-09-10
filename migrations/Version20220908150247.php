<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220908150247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_adresse (job_id INT NOT NULL, adresse_id INT NOT NULL, INDEX IDX_59B0BF8DBE04EA9 (job_id), INDEX IDX_59B0BF8D4DE7DC5C (adresse_id), PRIMARY KEY(job_id, adresse_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_adresse ADD CONSTRAINT FK_59B0BF8DBE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_adresse ADD CONSTRAINT FK_59B0BF8D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categoriesjobs ADD jobs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categoriesjobs ADD CONSTRAINT FK_B6DDFF7148704627 FOREIGN KEY (jobs_id) REFERENCES jobs (id)');
        $this->addSql('CREATE INDEX IDX_B6DDFF7148704627 ON categoriesjobs (jobs_id)');
        $this->addSql('ALTER TABLE jobs ADD region VARCHAR(60) DEFAULT NULL, ADD postale_code VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8DBE04EA9');
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('DROP TABLE job_adresse');
        $this->addSql('ALTER TABLE CategoriesJobs DROP FOREIGN KEY FK_B6DDFF7148704627');
        $this->addSql('DROP INDEX IDX_B6DDFF7148704627 ON CategoriesJobs');
        $this->addSql('ALTER TABLE CategoriesJobs DROP jobs_id');
        $this->addSql('ALTER TABLE jobs DROP region, DROP postale_code');
    }
}
