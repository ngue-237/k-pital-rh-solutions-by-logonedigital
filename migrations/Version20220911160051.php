<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220911160051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canditure ADD jobs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE canditure ADD CONSTRAINT FK_D5DE75F148704627 FOREIGN KEY (jobs_id) REFERENCES jobs (id)');
        $this->addSql('CREATE INDEX IDX_D5DE75F148704627 ON canditure (jobs_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canditure DROP FOREIGN KEY FK_D5DE75F148704627');
        $this->addSql('DROP INDEX IDX_D5DE75F148704627 ON canditure');
        $this->addSql('ALTER TABLE canditure DROP jobs_id');
    }
}
