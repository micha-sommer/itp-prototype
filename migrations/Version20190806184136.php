<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190806184136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice ADD registration_id INT NOT NULL, ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744833D8F43 FOREIGN KEY (registration_id) REFERENCES registrations (id)');
        $this->addSql('CREATE INDEX IDX_90651744833D8F43 ON invoice (registration_id)');
        $this->addSql('ALTER TABLE invoice_position ADD description VARCHAR(255) NOT NULL, ADD price INT NOT NULL, DROP is_add, CHANGE total_euro total INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744833D8F43');
        $this->addSql('DROP INDEX IDX_90651744833D8F43 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP registration_id, DROP published');
        $this->addSql('ALTER TABLE invoice_position ADD is_add TINYINT(1) NOT NULL, ADD total_euro INT NOT NULL, DROP total, DROP description, DROP price');
    }
}
