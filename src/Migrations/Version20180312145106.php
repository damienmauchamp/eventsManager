<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312145106 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE event CHANGE id id_event INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE event ADD PRIMARY KEY (id_event)');
        $this->addSql('ALTER TABLE user MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user CHANGE id id_user INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id_user)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event MODIFY id_event INT NOT NULL');
        $this->addSql('ALTER TABLE event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE event CHANGE id_event id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE event ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user MODIFY id_user INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user CHANGE id_user id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
    }
}
