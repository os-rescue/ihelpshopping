<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200329181236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE `ihs_user` (
              `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:uuid)\',
              `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
              `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `title` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `phone_number` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `mobile_number` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `created_at` datetime NOT NULL,
              `updated_at` datetime NOT NULL,
              `email_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
              `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
              `username_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
              `enabled` tinyint(1) NOT NULL DEFAULT \'0\',
              `locked` tinyint(1) NOT NULL DEFAULT \'0\',
              `salt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `last_login` datetime DEFAULT NULL,
              `confirmation_token` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `password_requested_at` datetime DEFAULT NULL,
              `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:array)\',
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_user_idx` (`email_canonical`),
              UNIQUE KEY `UNIQ_89FD485BC05FB297` (`confirmation_token`),
              KEY `first_name_idx` (`first_name`),
              KEY `last_name_idx` (`last_name`),
              KEY `middle_name_idx` (`middle_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $this->addSql('
            CREATE TABLE `refresh_tokens` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `refresh_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
              `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `valid` datetime NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function down(Schema $schema) : void
    {
    }
}
