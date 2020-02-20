<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200211143437 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notif DROP FOREIGN KEY FK_C0730D6B9514AA5C');
        $this->addSql('DROP INDEX IDX_C0730D6B9514AA5C ON notif');
        $this->addSql('ALTER TABLE notif CHANGE id_post_id post_id INT NOT NULL');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6B4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_C0730D6B4B89032C ON notif (post_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notif DROP FOREIGN KEY FK_C0730D6B4B89032C');
        $this->addSql('DROP INDEX IDX_C0730D6B4B89032C ON notif');
        $this->addSql('ALTER TABLE notif CHANGE post_id id_post_id INT NOT NULL');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6B9514AA5C FOREIGN KEY (id_post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_C0730D6B9514AA5C ON notif (id_post_id)');
    }
}
