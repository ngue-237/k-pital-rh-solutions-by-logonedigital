<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912165458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canditure (id INT AUTO_INCREMENT NOT NULL, jobs_id INT DEFAULT NULL, is_hired TINYINT(1) DEFAULT NULL, INDEX IDX_D5DE75F148704627 (jobs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, rgpd TINYINT(1) NOT NULL, is_blocked TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE canditure ADD CONSTRAINT FK_D5DE75F148704627 FOREIGN KEY (jobs_id) REFERENCES jobs (id)');
        $this->addSql('DROP TABLE contact');
        $this->addSql('ALTER TABLE categoriesjobs DROP FOREIGN KEY FK_B6DDFF7148704627');
        $this->addSql('DROP INDEX IDX_B6DDFF7148704627 ON categoriesjobs');
        $this->addSql('ALTER TABLE categoriesjobs ADD slug VARCHAR(255) NOT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD description LONGTEXT DEFAULT NULL, DROP jobs_id');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC540884D2 FOREIGN KEY (category_job_id) REFERENCES CategoriesJobs (id)');
        $this->addSql('CREATE INDEX IDX_A8936DC540884D2 ON jobs (category_job_id)');
        $this->addSql('ALTER TABLE job_adresse ADD CONSTRAINT FK_59B0BF8D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES Adresses (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, subject VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE canditure DROP FOREIGN KEY FK_D5DE75F148704627');
        $this->addSql('DROP TABLE canditure');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE CategoriesJobs ADD jobs_id INT DEFAULT NULL, DROP slug, DROP created_at, DROP description');
        $this->addSql('ALTER TABLE CategoriesJobs ADD CONSTRAINT FK_B6DDFF7148704627 FOREIGN KEY (jobs_id) REFERENCES jobs (id)');
        $this->addSql('CREATE INDEX IDX_B6DDFF7148704627 ON CategoriesJobs (jobs_id)');
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC540884D2');
        $this->addSql('DROP INDEX IDX_A8936DC540884D2 ON jobs');
    }
}
