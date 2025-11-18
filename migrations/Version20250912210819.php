<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912210819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

public function up(Schema $schema): void
{
    // Falls Spalten schon existieren, nicht nochmal hinzufügen
    // Nur Foreign Keys + Indizes setzen
    $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT FK_2BF9268485D19953 FOREIGN KEY (maintainer_id) REFERENCES user (id) ON DELETE SET NULL');
    $this->addSql('ALTER TABLE school_tour ADD CONSTRAINT FK_2BF926842D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL');
    $this->addSql('CREATE INDEX IF NOT EXISTS IDX_2BF9268485D19953 ON school_tour (maintainer_id)');
    $this->addSql('CREATE INDEX IF NOT EXISTS IDX_2BF926842D234F6A ON school_tour (approved_by_id)');
}


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY FK_2BF9268485D19953');
        $this->addSql('ALTER TABLE school_tour DROP FOREIGN KEY FK_2BF926842D234F6A');
        $this->addSql('DROP INDEX IDX_2BF9268485D19953 ON school_tour');
        $this->addSql('DROP INDEX IDX_2BF926842D234F6A ON school_tour');
    }
}
