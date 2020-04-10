<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200329145548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation_user (conversation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5AECB5559AC0396 (conversation_id), INDEX IDX_5AECB555A76ED395 (user_id), PRIMARY KEY(conversation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, conversation_id INT NOT NULL, author_id INT NOT NULL, media_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, is_read TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_B6BD307F9AC0396 (conversation_id), INDEX IDX_B6BD307FF675F31B (author_id), UNIQUE INDEX UNIQ_B6BD307FEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB5559AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB555A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE section_formation DROP FOREIGN KEY FK_220D513AEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_220D513AEA9FDD75 ON section_formation');
        $this->addSql('ALTER TABLE section_formation DROP media_id');
        $this->addSql('ALTER TABLE souscription ADD is_ended TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE etape_formation DROP FOREIGN KEY FK_6D539450EA9FDD75');
        $this->addSql('DROP INDEX UNIQ_6D539450EA9FDD75 ON etape_formation');
        $this->addSql('ALTER TABLE etape_formation DROP media_id');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_404021BFEA9FDD75 ON formation');
        $this->addSql('ALTER TABLE formation DROP media_id');
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

        $this->addSql('ALTER TABLE conversation_user DROP FOREIGN KEY FK_5AECB5559AC0396');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE etape_formation ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etape_formation ADD CONSTRAINT FK_6D539450EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D539450EA9FDD75 ON etape_formation (media_id)');
        $this->addSql('ALTER TABLE formation ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_404021BFEA9FDD75 ON formation (media_id)');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C5200282E');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C92ACB5FC');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CEDB2A4BD');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CE0ABDBC8');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C5200282E ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C92ACB5FC ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10CEDB2A4BD ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10CE0ABDBC8 ON media');
        $this->addSql('ALTER TABLE media DROP formation_id, DROP section_formation_id, DROP etape_formation_id, DROP etape_contenu_formation_id');
        $this->addSql('ALTER TABLE section_formation ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE section_formation ADD CONSTRAINT FK_220D513AEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_220D513AEA9FDD75 ON section_formation (media_id)');
        $this->addSql('ALTER TABLE souscription DROP is_ended');
    }
}
