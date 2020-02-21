<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220172312 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE souscription (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, user_id INT NOT NULL, billing_period_ends_at DATETIME DEFAULT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, ends_at DATETIME DEFAULT NULL, INDEX IDX_2AED620D53C674EE (offer_id), UNIQUE INDEX UNIQ_2AED620DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_event_log (id INT AUTO_INCREMENT NOT NULL, stripe_event_id VARCHAR(255) NOT NULL, handled_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE souscription ADD CONSTRAINT FK_2AED620D53C674EE FOREIGN KEY (offer_id) REFERENCES offers (id)');
        $this->addSql('ALTER TABLE souscription ADD CONSTRAINT FK_2AED620DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD stripe_customer_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649708DC647 ON user (stripe_customer_id)');
        $this->addSql('ALTER TABLE offers ADD plan VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE souscription');
        $this->addSql('DROP TABLE stripe_event_log');
        $this->addSql('ALTER TABLE offers DROP plan');
        $this->addSql('DROP INDEX UNIQ_8D93D649708DC647 ON user');
        $this->addSql('ALTER TABLE user DROP stripe_customer_id');
    }
}
