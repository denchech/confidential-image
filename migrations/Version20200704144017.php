<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200704144017 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD uuid VARCHAR(180) NOT NULL, ADD openings_number INT NOT NULL, ADD max_openings_number INT DEFAULT NULL, ADD expires_at DATE NOT NULL, ADD openings LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE slug password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FD17F50A6 ON image (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C53D045FD17F50A6 ON image');
        $this->addSql('ALTER TABLE image DROP uuid, DROP openings_number, DROP max_openings_number, DROP expires_at, DROP openings, CHANGE password slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
