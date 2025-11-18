<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914112858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_tour ADD start_latitude DOUBLE PRECISION DEFAULT NULL, ADD start_longitude DOUBLE PRECISION DEFAULT NULL, ADD end_latitude DOUBLE PRECISION DEFAULT NULL, ADD end_longitude DOUBLE PRECISION DEFAULT NULL, ADD companion_requirement VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_tour DROP start_latitude, DROP start_longitude, DROP end_latitude, DROP end_longitude, DROP companion_requirement');
    }
}
