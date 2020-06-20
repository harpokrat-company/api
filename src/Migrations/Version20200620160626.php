<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200620160626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE organization_group_organization_group');
        $this->addSql('ALTER TABLE secure_action CHANGE action action JSON NOT NULL');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_E347A2025E237E06 ON organization_group');
        $this->addSql('ALTER TABLE organization_group ADD parent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A202727ACA70 FOREIGN KEY (parent_id) REFERENCES organization_group (id)');
        $this->addSql('CREATE INDEX IDX_E347A202727ACA70 ON organization_group (parent_id)');
        $this->addSql('DROP INDEX UNIQ_C1EE637C5E237E06 ON organization');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization_group_organization_group (organization_group_source CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', organization_group_target CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_F06C1B31520E033C (organization_group_target), INDEX IDX_F06C1B314BEB53B3 (organization_group_source), PRIMARY KEY(organization_group_source, organization_group_target)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE organization_group_organization_group ADD CONSTRAINT FK_F06C1B314BEB53B3 FOREIGN KEY (organization_group_source) REFERENCES organization_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_group_organization_group ADD CONSTRAINT FK_F06C1B31520E033C FOREIGN KEY (organization_group_target) REFERENCES organization_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637C5E237E06 ON organization (name)');
        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A202727ACA70');
        $this->addSql('DROP INDEX IDX_E347A202727ACA70 ON organization_group');
        $this->addSql('ALTER TABLE organization_group DROP parent_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E347A2025E237E06 ON organization_group (name)');
        $this->addSql('ALTER TABLE secure_action CHANGE action action LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
