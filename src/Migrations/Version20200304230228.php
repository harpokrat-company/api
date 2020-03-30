<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200304230228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization_group (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', organization_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_E347A2025E237E06 (name), INDEX IDX_E347A20232C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', owner_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_C1EE637C5E237E06 (name), INDEX IDX_C1EE637C7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A20232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE secure_action CHANGE action action JSON NOT NULL');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A20232C8A3DE');
        $this->addSql('DROP TABLE organization_group');
        $this->addSql('DROP TABLE organization');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE secure_action CHANGE action action LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE first_name first_name VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE last_name last_name VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci');
    }
}
