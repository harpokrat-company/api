<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200626204652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vault_secret_ownership (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', vault_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9DAF6D458AC2DF8 (vault_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE secret_ownership (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization_group_secret_ownership (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9A9EAC93FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_secret_ownership (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_742189A0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vault_secret_ownership ADD CONSTRAINT FK_9DAF6D458AC2DF8 FOREIGN KEY (vault_id) REFERENCES vault (id)');
        $this->addSql('ALTER TABLE vault_secret_ownership ADD CONSTRAINT FK_9DAF6D4BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_group_secret_ownership ADD CONSTRAINT FK_9A9EAC93FE54D947 FOREIGN KEY (group_id) REFERENCES organization_group (id)');
        $this->addSql('ALTER TABLE organization_group_secret_ownership ADD CONSTRAINT FK_9A9EAC93BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_secret_ownership ADD CONSTRAINT FK_742189A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_secret_ownership ADD CONSTRAINT FK_742189A0BF396750 FOREIGN KEY (id) REFERENCES secret_ownership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secure_action CHANGE action action JSON NOT NULL');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization_group CHANGE parent_id parent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE secret DROP FOREIGN KEY FK_5CA2E8E57E3C61F9');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT FK_5CA2E8E57E3C61F9 FOREIGN KEY (owner_id) REFERENCES secret_ownership (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vault_secret_ownership DROP FOREIGN KEY FK_9DAF6D4BF396750');
        $this->addSql('ALTER TABLE organization_group_secret_ownership DROP FOREIGN KEY FK_9A9EAC93BF396750');
        $this->addSql('ALTER TABLE user_secret_ownership DROP FOREIGN KEY FK_742189A0BF396750');
        $this->addSql('ALTER TABLE secret DROP FOREIGN KEY FK_5CA2E8E57E3C61F9');
        $this->addSql('DROP TABLE vault_secret_ownership');
        $this->addSql('DROP TABLE secret_ownership');
        $this->addSql('DROP TABLE organization_group_secret_ownership');
        $this->addSql('DROP TABLE user_secret_ownership');
        $this->addSql('ALTER TABLE log CHANGE user_id user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE uri uri VARCHAR(2047) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization_group CHANGE parent_id parent_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE secret DROP FOREIGN KEY FK_5CA2E8E57E3C61F9');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT FK_5CA2E8E57E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE secure_action CHANGE action action LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
