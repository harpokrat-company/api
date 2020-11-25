<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125223127 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE organization_group_secret_ownership');
        $this->addSql('DROP TABLE organization_group_vault_ownership');
        $this->addSql('DROP TABLE user_secret_ownership');
        $this->addSql('DROP TABLE user_vault_ownership');
        $this->addSql('DROP TABLE vault_secret_ownership');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A20232C8A3DE');
        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A202727ACA70');
        $this->addSql('ALTER TABLE organization_group CHANGE organization_id organization_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE parent_id parent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A20232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A202727ACA70 FOREIGN KEY (parent_id) REFERENCES organization_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secret DROP FOREIGN KEY FK_5CA2E8E57E3C61F9');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT FK_5CA2E8E57E3C61F9 FOREIGN KEY (owner_id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secret_ownership ADD group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD vault_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE secret_ownership ADD CONSTRAINT FK_934CB7DFE54D947 FOREIGN KEY (group_id) REFERENCES organization_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secret_ownership ADD CONSTRAINT FK_934CB7DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secret_ownership ADD CONSTRAINT FK_934CB7D58AC2DF8 FOREIGN KEY (vault_id) REFERENCES vault (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_934CB7DFE54D947 ON secret_ownership (group_id)');
        $this->addSql('CREATE INDEX IDX_934CB7DA76ED395 ON secret_ownership (user_id)');
        $this->addSql('CREATE INDEX IDX_934CB7D58AC2DF8 ON secret_ownership (vault_id)');
        $this->addSql('ALTER TABLE secure_action CHANGE action action JSON NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE encryption_key_id encryption_key_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE vault DROP FOREIGN KEY FK_FF3049217E3C61F9');
        $this->addSql('ALTER TABLE vault CHANGE encryption_key_id encryption_key_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vault ADD CONSTRAINT FK_FF3049217E3C61F9 FOREIGN KEY (owner_id) REFERENCES vault_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vault_ownership ADD group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vault_ownership ADD CONSTRAINT FK_90598976FE54D947 FOREIGN KEY (group_id) REFERENCES organization_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vault_ownership ADD CONSTRAINT FK_90598976A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_90598976FE54D947 ON vault_ownership (group_id)');
        $this->addSql('CREATE INDEX IDX_90598976A76ED395 ON vault_ownership (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization_group_secret_ownership (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', group_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9A9EAC93FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE organization_group_vault_ownership (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', group_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_33C1FAFBFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_secret_ownership (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_742189A0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_vault_ownership (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_8285E5F9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vault_secret_ownership (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', vault_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9DAF6D458AC2DF8 (vault_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE organization_group_secret_ownership ADD CONSTRAINT FK_9A9EAC93BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_group_secret_ownership ADD CONSTRAINT FK_9A9EAC93FE54D947 FOREIGN KEY (group_id) REFERENCES organization_group (id)');
        $this->addSql('ALTER TABLE organization_group_vault_ownership ADD CONSTRAINT FK_33C1FAFBBF396750 FOREIGN KEY (id) REFERENCES vault_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_group_vault_ownership ADD CONSTRAINT FK_33C1FAFBFE54D947 FOREIGN KEY (group_id) REFERENCES organization_group (id)');
        $this->addSql('ALTER TABLE user_secret_ownership ADD CONSTRAINT FK_742189A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_secret_ownership ADD CONSTRAINT FK_742189A0BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_vault_ownership ADD CONSTRAINT FK_8285E5F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_vault_ownership ADD CONSTRAINT FK_8285E5F9BF396750 FOREIGN KEY (id) REFERENCES vault_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vault_secret_ownership ADD CONSTRAINT FK_9DAF6D458AC2DF8 FOREIGN KEY (vault_id) REFERENCES vault (id)');
        $this->addSql('ALTER TABLE vault_secret_ownership ADD CONSTRAINT FK_9DAF6D4BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A20232C8A3DE');
        $this->addSql('ALTER TABLE organization_group DROP FOREIGN KEY FK_E347A202727ACA70');
        $this->addSql('ALTER TABLE organization_group CHANGE organization_id organization_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE parent_id parent_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A20232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization_group ADD CONSTRAINT FK_E347A202727ACA70 FOREIGN KEY (parent_id) REFERENCES organization_group (id)');
        $this->addSql('ALTER TABLE secret DROP FOREIGN KEY FK_5CA2E8E57E3C61F9');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT FK_5CA2E8E57E3C61F9 FOREIGN KEY (owner_id) REFERENCES secret_ownership (id)');
        $this->addSql('ALTER TABLE secret_ownership DROP FOREIGN KEY FK_934CB7DFE54D947');
        $this->addSql('ALTER TABLE secret_ownership DROP FOREIGN KEY FK_934CB7DA76ED395');
        $this->addSql('ALTER TABLE secret_ownership DROP FOREIGN KEY FK_934CB7D58AC2DF8');
        $this->addSql('DROP INDEX IDX_934CB7DFE54D947 ON secret_ownership');
        $this->addSql('DROP INDEX IDX_934CB7DA76ED395 ON secret_ownership');
        $this->addSql('DROP INDEX IDX_934CB7D58AC2DF8 ON secret_ownership');
        $this->addSql('ALTER TABLE secret_ownership DROP group_id, DROP user_id, DROP vault_id');
        $this->addSql('ALTER TABLE secure_action CHANGE action action LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user CHANGE encryption_key_id encryption_key_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE vault DROP FOREIGN KEY FK_FF3049217E3C61F9');
        $this->addSql('ALTER TABLE vault CHANGE encryption_key_id encryption_key_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vault ADD CONSTRAINT FK_FF3049217E3C61F9 FOREIGN KEY (owner_id) REFERENCES vault_ownership (id)');
        $this->addSql('ALTER TABLE vault_ownership DROP FOREIGN KEY FK_90598976FE54D947');
        $this->addSql('ALTER TABLE vault_ownership DROP FOREIGN KEY FK_90598976A76ED395');
        $this->addSql('DROP INDEX IDX_90598976FE54D947 ON vault_ownership');
        $this->addSql('DROP INDEX IDX_90598976A76ED395 ON vault_ownership');
        $this->addSql('ALTER TABLE vault_ownership DROP group_id, DROP user_id');
    }
}
