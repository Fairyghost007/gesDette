<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219003020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE detail_dette (id SERIAL NOT NULL, dette_id INT NOT NULL, article_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, quantite_dette INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEEB0351E11400A1 ON detail_dette (dette_id)');
        $this->addSql('CREATE INDEX IDX_FEEB03517294869C ON detail_dette (article_id)');
        $this->addSql('ALTER TABLE detail_dette ADD CONSTRAINT FK_FEEB0351E11400A1 FOREIGN KEY (dette_id) REFERENCES dette (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE detail_dette ADD CONSTRAINT FK_FEEB03517294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE detail_dette DROP CONSTRAINT FK_FEEB0351E11400A1');
        $this->addSql('ALTER TABLE detail_dette DROP CONSTRAINT FK_FEEB03517294869C');
        $this->addSql('DROP TABLE detail_dette');
    }
}
