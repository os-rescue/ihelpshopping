<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415123937 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ihs_requester_shopping_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4FE0EA0FDE12AB56 (created_by), INDEX name_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ihs_helper_shopping_item (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', helper_requester_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', requester_shopping_item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_835F72D4405CEF78 (helper_requester_id), INDEX IDX_835F72D49B2DCDFA (requester_shopping_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ihs_requester_shopping_item ADD CONSTRAINT FK_4FE0EA0FDE12AB56 FOREIGN KEY (created_by) REFERENCES ihs_user (id)');
        $this->addSql('ALTER TABLE ihs_helper_shopping_item ADD CONSTRAINT FK_835F72D4405CEF78 FOREIGN KEY (helper_requester_id) REFERENCES ihs_helper_requester (id)');
        $this->addSql('ALTER TABLE ihs_helper_shopping_item ADD CONSTRAINT FK_835F72D49B2DCDFA FOREIGN KEY (requester_shopping_item_id) REFERENCES ihs_requester_shopping_item (id)');
        $this->addSql('DROP TABLE ihs_shopping_item');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ihs_helper_shopping_item DROP FOREIGN KEY FK_835F72D49B2DCDFA');
        $this->addSql('CREATE TABLE ihs_shopping_item (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', created_by CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9E36C0C5DE12AB56 (created_by), INDEX name_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ihs_shopping_item ADD CONSTRAINT FK_9E36C0C5DE12AB56 FOREIGN KEY (created_by) REFERENCES ihs_user (id)');
        $this->addSql('DROP TABLE ihs_requester_shopping_item');
        $this->addSql('DROP TABLE ihs_helper_shopping_item');
    }
}
