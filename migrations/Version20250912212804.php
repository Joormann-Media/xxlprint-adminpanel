<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912212804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY `FK_2BF926842D234F6A`');
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY `FK_2BF9268485D19953`');
        $this->addSql('ALTER TABLE school_tour ADD distance DOUBLE PRECISION DEFAULT NULL, ADD duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT FK_2BF926842D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT FK_2BF9268485D19953 FOREIGN KEY (maintainer_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY FK_2BF9268485D19953');
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY FK_2BF926842D234F6A');
        $this->addSql('ALTER TABLE school_tour DROP distance, DROP duration');
        $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT `FK_2BF9268485D19953` FOREIGN KEY (maintainer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT `FK_2BF926842D234F6A` FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }
}
