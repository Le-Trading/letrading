<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200328102356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE etape_formation (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, position INT NOT NULL, INDEX IDX_6D539450D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_formation (id INT AUTO_INCREMENT NOT NULL, formation_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_220D513A5200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etape_formation ADD CONSTRAINT FK_6D539450D823E37A FOREIGN KEY (section_id) REFERENCES section_formation (id)');
        $this->addSql('ALTER TABLE section_formation ADD CONSTRAINT FK_220D513A5200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE media ADD formation_id INT DEFAULT NULL, ADD section_formation_id INT DEFAULT NULL, ADD etape_formation_id INT DEFAULT NULL, ADD etape_contenu_formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C5200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C92ACB5FC FOREIGN KEY (section_formation_id) REFERENCES section_formation (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CEDB2A4BD FOREIGN KEY (etape_formation_id) REFERENCES etape_formation (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CE0ABDBC8 FOREIGN KEY (etape_contenu_formation_id) REFERENCES etape_formation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C5200282E ON media (formation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C92ACB5FC ON media (section_formation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10CEDB2A4BD ON media (etape_formation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10CE0ABDBC8 ON media (etape_contenu_formation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CEDB2A4BD');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CE0ABDBC8');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C5200282E');
        $this->addSql('ALTER TABLE section_formation DROP FOREIGN KEY FK_220D513A5200282E');
        $this->addSql('ALTER TABLE etape_formation DROP FOREIGN KEY FK_6D539450D823E37A');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C92ACB5FC');
        $this->addSql('DROP TABLE etape_formation');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE section_formation');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C5200282E ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C92ACB5FC ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10CEDB2A4BD ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10CE0ABDBC8 ON media');
        $this->addSql('ALTER TABLE media DROP formation_id, DROP section_formation_id, DROP etape_formation_id, DROP etape_contenu_formation_id');
    }
}
