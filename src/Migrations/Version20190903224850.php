<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190903224850 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE email_validation_date email_validation_date DATETIME DEFAULT NULL, CHANGE email_validation_mail_sent_date email_validation_mail_sent_date DATETIME DEFAULT NULL, CHANGE email_validation_code email_validation_code VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE email_validation_date email_validation_date DATETIME DEFAULT \'NULL\', CHANGE email_validation_mail_sent_date email_validation_mail_sent_date DATETIME DEFAULT \'NULL\', CHANGE email_validation_code email_validation_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}