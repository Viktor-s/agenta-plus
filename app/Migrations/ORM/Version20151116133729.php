<?php

namespace AgentPlus\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151116133729 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE catalog (id UUID NOT NULL, creator_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B2C324761220EA6 ON catalog (creator_id)');
        $this->addSql('CREATE TABLE catalog_factories (catalog_id UUID NOT NULL, factory_id UUID NOT NULL, PRIMARY KEY(catalog_id, factory_id))');
        $this->addSql('CREATE INDEX IDX_493578F7CC3C66FC ON catalog_factories (catalog_id)');
        $this->addSql('CREATE INDEX IDX_493578F7C7AF27D2 ON catalog_factories (factory_id)');
        $this->addSql('CREATE TABLE got_catalog (id UUID NOT NULL, catalog_id UUID NOT NULL, diary_id UUID NOT NULL, client_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_958DF320CC3C66FC ON got_catalog (catalog_id)');
        $this->addSql('CREATE INDEX IDX_958DF320E020E47A ON got_catalog (diary_id)');
        $this->addSql('CREATE INDEX IDX_958DF32019EB6921 ON got_catalog (client_id)');
        $this->addSql('CREATE TABLE catalog_image (id UUID NOT NULL, catalog_id UUID NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, size BIGINT NOT NULL, mime_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C2E65E6CC3C66FC ON catalog_image (catalog_id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C324761220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_factories ADD CONSTRAINT FK_493578F7CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_factories ADD CONSTRAINT FK_493578F7C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE got_catalog ADD CONSTRAINT FK_958DF320CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE got_catalog ADD CONSTRAINT FK_958DF320E020E47A FOREIGN KEY (diary_id) REFERENCES diary (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE got_catalog ADD CONSTRAINT FK_958DF32019EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_image ADD CONSTRAINT FK_7C2E65E6CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE catalog_factories DROP CONSTRAINT FK_493578F7CC3C66FC');
        $this->addSql('ALTER TABLE got_catalog DROP CONSTRAINT FK_958DF320CC3C66FC');
        $this->addSql('ALTER TABLE catalog_image DROP CONSTRAINT FK_7C2E65E6CC3C66FC');
        $this->addSql('DROP TABLE catalog');
        $this->addSql('DROP TABLE catalog_factories');
        $this->addSql('DROP TABLE got_catalog');
        $this->addSql('DROP TABLE catalog_image');
    }
}
