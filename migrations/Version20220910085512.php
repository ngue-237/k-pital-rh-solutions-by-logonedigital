<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220910085512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('CREATE TABLE Adresses (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postale_code VARCHAR(30) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('ALTER TABLE jobs DROP region, DROP postale_code');
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('ALTER TABLE job_adresse ADD CONSTRAINT FK_59B0BF8D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES Adresses (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE Adresses');
        $this->addSql('DROP TABLE contact');
        $this->addSql('ALTER TABLE jobs ADD region VARCHAR(60) DEFAULT NULL, ADD postale_code VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_adresse DROP FOREIGN KEY FK_59B0BF8D4DE7DC5C');
        $this->addSql('ALTER TABLE job_adresse ADD CONSTRAINT FK_59B0BF8D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id) ON DELETE CASCADE');
    }
}
