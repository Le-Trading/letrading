<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302160236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE follow_user');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_683444707FEA59C0');
        $this->addSql('DROP INDEX IDX_683444707FEA59C0 ON follow');
        $this->addSql('ALTER TABLE follow ADD follower_id INT NOT NULL, ADD followed_id INT NOT NULL, DROP suivi_id');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470D956F010 FOREIGN KEY (followed_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_68344470AC24F853 ON follow (follower_id)');
        $this->addSql('CREATE INDEX IDX_68344470D956F010 ON follow (followed_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE follow_user (follow_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B35425148711D3BC (follow_id), INDEX IDX_B3542514A76ED395 (user_id), PRIMARY KEY(follow_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE follow_user ADD CONSTRAINT FK_B35425148711D3BC FOREIGN KEY (follow_id) REFERENCES follow (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow_user ADD CONSTRAINT FK_B3542514A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470AC24F853');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470D956F010');
        $this->addSql('DROP INDEX IDX_68344470AC24F853 ON follow');
        $this->addSql('DROP INDEX IDX_68344470D956F010 ON follow');
        $this->addSql('ALTER TABLE follow ADD suivi_id INT DEFAULT NULL, DROP follower_id, DROP followed_id');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_683444707FEA59C0 FOREIGN KEY (suivi_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_683444707FEA59C0 ON follow (suivi_id)');
    }
}
