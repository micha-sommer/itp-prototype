<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181009132838 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contestants CHANGE itc itc ENUM(\'no\', \'su-tu\', \'su-we\')');
        $this->addSql('ALTER TABLE officials CHANGE itc itc ENUM(\'no\', \'su-tu\', \'su-we\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contestants CHANGE itc itc TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE officials CHANGE itc itc TINYINT(1) NOT NULL');
    }
}
