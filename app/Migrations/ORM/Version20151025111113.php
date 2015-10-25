<?php

namespace AgentPlus\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151025111113 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE client (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(2) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, address TEXT DEFAULT NULL, phones JSON NOT NULL, emails JSON NOT NULL, notes VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client_invoices (id UUID NOT NULL, client_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX client_invoice_client_idx ON client_invoices (client_id)');
        $this->addSql('CREATE TABLE currency (code VARCHAR(3) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE UNIQUE INDEX currency_unique ON currency (code)');
        $this->addSql('CREATE TABLE diary (id UUID NOT NULL, creator_id UUID NOT NULL, order_id UUID DEFAULT NULL, client_id UUID DEFAULT NULL, stage_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, comment TEXT DEFAULT NULL, money_amount NUMERIC(10, 4) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_917BEDE261220EA6 ON diary (creator_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE28D9F6D38 ON diary (order_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE219EB6921 ON diary (client_id)');
        $this->addSql('CREATE INDEX IDX_917BEDE22298D193 ON diary (stage_id)');
        $this->addSql('CREATE TABLE diary_factories (diary_id UUID NOT NULL, factory_id UUID NOT NULL, PRIMARY KEY(diary_id, factory_id))');
        $this->addSql('CREATE INDEX IDX_BE7F78E9E020E47A ON diary_factories (diary_id)');
        $this->addSql('CREATE INDEX IDX_BE7F78E9C7AF27D2 ON diary_factories (factory_id)');
        $this->addSql('CREATE TABLE factory (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE orders (id UUID NOT NULL, client_id UUID NOT NULL, stage_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, money_amount NUMERIC(10, 4) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEE19EB6921 ON orders (client_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE2298D193 ON orders (stage_id)');
        $this->addSql('CREATE TABLE stage (id UUID NOT NULL, label VARCHAR(255) NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE team (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE team_user (team_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(team_id, user_id))');
        $this->addSql('CREATE INDEX IDX_5C722232296CD8AE ON team_user (team_id)');
        $this->addSql('CREATE INDEX IDX_5C722232A76ED395 ON team_user (user_id)');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type SMALLINT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX users_username_canonical_idx ON users (username_canonical)');
        $this->addSql('CREATE INDEX users_email_canonical_idx ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX users_username_canonical_unique ON users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX users_email_canonical_unique ON users (email_canonical)');
        $this->addSql('ALTER TABLE client_invoices ADD CONSTRAINT FK_44C91F5019EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary ADD CONSTRAINT FK_917BEDE261220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary ADD CONSTRAINT FK_917BEDE28D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary ADD CONSTRAINT FK_917BEDE219EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary ADD CONSTRAINT FK_917BEDE22298D193 FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary_factories ADD CONSTRAINT FK_BE7F78E9E020E47A FOREIGN KEY (diary_id) REFERENCES diary (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diary_factories ADD CONSTRAINT FK_BE7F78E9C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2298D193 FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE client_invoices DROP CONSTRAINT FK_44C91F5019EB6921');
        $this->addSql('ALTER TABLE diary DROP CONSTRAINT FK_917BEDE219EB6921');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE19EB6921');
        $this->addSql('ALTER TABLE diary_factories DROP CONSTRAINT FK_BE7F78E9E020E47A');
        $this->addSql('ALTER TABLE diary_factories DROP CONSTRAINT FK_BE7F78E9C7AF27D2');
        $this->addSql('ALTER TABLE diary DROP CONSTRAINT FK_917BEDE28D9F6D38');
        $this->addSql('ALTER TABLE diary DROP CONSTRAINT FK_917BEDE22298D193');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE2298D193');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE diary DROP CONSTRAINT FK_917BEDE261220EA6');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232A76ED395');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE client_invoices');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE diary');
        $this->addSql('DROP TABLE diary_factories');
        $this->addSql('DROP TABLE factory');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE stage');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE users');
    }
}
