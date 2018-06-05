<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180605220812 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE officials CHANGE registration_id registration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE officials ADD CONSTRAINT FK_6527C484833D8F43 FOREIGN KEY (registration_id) REFERENCES registrations (id)');
        $this->addSql('CREATE INDEX IDX_6527C484833D8F43 ON officials (registration_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE officials DROP FOREIGN KEY FK_6527C484833D8F43');
        $this->addSql('DROP INDEX IDX_6527C484833D8F43 ON officials');
        $this->addSql('ALTER TABLE officials CHANGE registration_id registration_id INT NOT NULL');
    }
}
