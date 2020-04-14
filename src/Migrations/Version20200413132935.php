<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413132935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ihs_helper_requester (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', helper_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', requester_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E6239B34D7693E95 (helper_id), INDEX IDX_E6239B34ED442CF4 (requester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ihs_helper_requester ADD CONSTRAINT FK_E6239B34D7693E95 FOREIGN KEY (helper_id) REFERENCES ihs_user (id)');
        $this->addSql('ALTER TABLE ihs_helper_requester ADD CONSTRAINT FK_E6239B34ED442CF4 FOREIGN KEY (requester_id) REFERENCES ihs_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ihs_helper_requester');
    }
}
