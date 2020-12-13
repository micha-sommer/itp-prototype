<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404184803 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, registration_id INT NOT NULL, paid_cash_euro INT NOT NULL, paid_cash_cent SMALLINT NOT NULL, paid_bank_euro INT NOT NULL, paid_bank_cent SMALLINT NOT NULL, UNIQUE INDEX UNIQ_90651744833D8F43 (registration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) DEFAULT NULL, amount_euro INT NOT NULL, amount_cent SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_position (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, invoice_id INT NOT NULL, multiplier NUMERIC(5, 2) NOT NULL, is_add TINYINT(1) NOT NULL, total_euro INT NOT NULL, total_cent SMALLINT NOT NULL, INDEX IDX_5904BEAD126F525E (item_id), INDEX IDX_5904BEAD2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744833D8F43 FOREIGN KEY (registration_id) REFERENCES registrations (id)');
        $this->addSql('ALTER TABLE invoice_position ADD CONSTRAINT FK_5904BEAD126F525E FOREIGN KEY (item_id) REFERENCES invoice_item (id)');
        $this->addSql('ALTER TABLE invoice_position ADD CONSTRAINT FK_5904BEAD2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE registrations ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT FK_53DE51E72989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53DE51E72989F1FD ON registrations (invoice_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice_position DROP FOREIGN KEY FK_5904BEAD2989F1FD');
        $this->addSql('ALTER TABLE registrations DROP FOREIGN KEY FK_53DE51E72989F1FD');
        $this->addSql('ALTER TABLE invoice_position DROP FOREIGN KEY FK_5904BEAD126F525E');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE invoice_position');
        $this->addSql('DROP INDEX UNIQ_53DE51E72989F1FD ON registrations');
        $this->addSql('ALTER TABLE registrations DROP invoice_id');
    }
}
