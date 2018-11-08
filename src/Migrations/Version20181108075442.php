<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181108075442 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contestants CHANGE weight_category weight_category ENUM(\'-40\',\'-44\',\'-48\',\'-52\',\'-57\',\'-63\',\'-70\',\'+70\',\'-78\',\'+78\',\'camp_only\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contestants CHANGE weight_category weight_category ENUM(\'-40\',\'-44\',\'-48\',\'-52\',\'-57\',\'-63\',\'-70\',\'+70\',\'-78\',\'+78\')');
    }
}
