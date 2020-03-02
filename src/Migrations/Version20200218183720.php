<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218183720 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3B91B7B5');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3B91B7B5 FOREIGN KEY (respond_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3B91B7B5');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3B91B7B5 FOREIGN KEY (respond_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE user DROP created_at');
    }
}
