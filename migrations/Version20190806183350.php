<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190806183350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice_position DROP FOREIGN KEY FK_5904BEAD126F525E');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744833D8F43');
        $this->addSql('DROP INDEX UNIQ_90651744833D8F43 ON invoice');
        $this->addSql('ALTER TABLE invoice ADD total INT NOT NULL, DROP registration_id, DROP paid_cash_euro, DROP paid_bank_euro, DROP total_euro');
        $this->addSql('DROP INDEX IDX_5904BEAD126F525E ON invoice_position');
        $this->addSql('ALTER TABLE invoice_position DROP item_id');
        $this->addSql('ALTER TABLE registrations DROP FOREIGN KEY FK_53DE51E72989F1FD');
        $this->addSql('DROP INDEX UNIQ_53DE51E72989F1FD ON registrations');
        $this->addSql('ALTER TABLE registrations DROP invoice_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, amount_euro INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE invoice ADD paid_cash_euro INT NOT NULL, ADD paid_bank_euro INT NOT NULL, ADD total_euro INT NOT NULL, CHANGE total registration_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744833D8F43 FOREIGN KEY (registration_id) REFERENCES registrations (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90651744833D8F43 ON invoice (registration_id)');
        $this->addSql('ALTER TABLE invoice_position ADD item_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice_position ADD CONSTRAINT FK_5904BEAD126F525E FOREIGN KEY (item_id) REFERENCES invoice_item (id)');
        $this->addSql('CREATE INDEX IDX_5904BEAD126F525E ON invoice_position (item_id)');
        $this->addSql('ALTER TABLE registrations ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT FK_53DE51E72989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53DE51E72989F1FD ON registrations (invoice_id)');
    }
}
