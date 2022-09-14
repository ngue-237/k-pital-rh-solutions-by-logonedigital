<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220914154747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canditure DROP FOREIGN KEY FK_D5DE75F148704627');
        $this->addSql('DROP TABLE canditure');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canditure (id INT AUTO_INCREMENT NOT NULL, jobs_id INT DEFAULT NULL, is_hired TINYINT(1) DEFAULT NULL, INDEX IDX_D5DE75F148704627 (jobs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE canditure ADD CONSTRAINT FK_D5DE75F148704627 FOREIGN KEY (jobs_id) REFERENCES jobs (id)');
    }
}
