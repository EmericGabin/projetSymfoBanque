<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116145702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD limit_decouvert INT NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE debit_account_id debit_account_id INT NOT NULL, CHANGE credit_account_id credit_account_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP limit_decouvert');
        $this->addSql('ALTER TABLE transaction CHANGE debit_account_id debit_account_id INT DEFAULT NULL, CHANGE credit_account_id credit_account_id INT DEFAULT NULL');
    }
}
