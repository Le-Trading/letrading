<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124142157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE post_vote (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9345E26F4B89032C (post_id), INDEX IDX_9345E26FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, thread_id INT NOT NULL, respond_id INT DEFAULT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, is_admin TINYINT(1) NOT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), INDEX IDX_5A8A6C8DE2904019 (thread_id), INDEX IDX_5A8A6C8D3B91B7B5 (respond_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, post_id INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, image_size INT NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6A2CA10CA76ED395 (user_id), UNIQUE INDEX UNIQ_6A2CA10C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_9345E26F4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_9345E26FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3B91B7B5 FOREIGN KEY (respond_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY FK_9345E26F4B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3B91B7B5');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DE2904019');
        $this->addSql('DROP TABLE post_vote');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE media');
    }
}
