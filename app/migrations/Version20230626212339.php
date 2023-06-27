<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230626212339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ratings (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, author_id INT NOT NULL, value INT NOT NULL, INDEX IDX_CEB607C959D8A214 (recipe_id), INDEX IDX_CEB607C9F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C959D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id)');
        $this->addSql('ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AF675F31B');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C959D8A214');
        $this->addSql('ALTER TABLE ratings DROP FOREIGN KEY FK_CEB607C9F675F31B');
        $this->addSql('DROP TABLE ratings');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AF675F31B');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
