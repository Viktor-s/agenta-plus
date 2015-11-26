<?php

namespace AgentPlus\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151124071528 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE diary_type (id UUID NOT NULL, parent_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97041682727ACA70 ON diary_type (parent_id)');
        $this->addSql('ALTER TABLE diary_type ADD CONSTRAINT FK_97041682727ACA70 FOREIGN KEY (parent_id) REFERENCES diary_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary ADD type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE diary ADD CONSTRAINT FK_917BEDE2C54C8C93 FOREIGN KEY (type_id) REFERENCES diary_type (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_917BEDE2C54C8C93 ON diary (type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE diary DROP CONSTRAINT FK_917BEDE2C54C8C93');
        $this->addSql('ALTER TABLE diary_type DROP CONSTRAINT FK_97041682727ACA70');
        $this->addSql('DROP TABLE diary_type');
        $this->addSql('DROP INDEX IDX_917BEDE2C54C8C93');
        $this->addSql('ALTER TABLE diary DROP type_id');
    }
}
