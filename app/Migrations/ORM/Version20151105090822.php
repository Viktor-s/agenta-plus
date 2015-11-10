<?php

namespace AgentPlus\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151105090822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE orders ADD currency VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders RENAME COLUMN money_amount TO amount');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6956883F FOREIGN KEY (currency) REFERENCES currency (code) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6956883F ON orders (currency)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6956883F');
        $this->addSql('DROP INDEX IDX_E52FFDEE6956883F');
        $this->addSql('ALTER TABLE orders DROP currency');
        $this->addSql('ALTER TABLE orders RENAME COLUMN amount TO money_amount');
    }
}
