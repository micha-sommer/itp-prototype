<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190723203412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP paid_cash_cent, DROP paid_bank_cent');
        $this->addSql('ALTER TABLE invoice_item DROP amount_cent');
        $this->addSql('ALTER TABLE invoice_position DROP total_cent');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice ADD paid_cash_cent SMALLINT NOT NULL, ADD paid_bank_cent SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE invoice_item ADD amount_cent SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE invoice_position ADD total_cent SMALLINT NOT NULL');
    }
}
