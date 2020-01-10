<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110162155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36104B89032C');
        $this->addSql('ALTER TABLE post_vote DROP FOREIGN KEY FK_9345E26F4B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DE2904019');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_vote');
        $this->addSql('DROP TABLE thread');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, original_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, extension VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, size BIGINT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8C9F36104B89032C (post_id), UNIQUE INDEX UNIQ_8C9F3610A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, thread_id INT NOT NULL, created_at DATETIME NOT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_admin TINYINT(1) NOT NULL, INDEX IDX_5A8A6C8DE2904019 (thread_id), INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE post_vote (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9345E26FA76ED395 (user_id), INDEX IDX_9345E26F4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE thread (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36104B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_9345E26F4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_vote ADD CONSTRAINT FK_9345E26FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_user');
    }
}
