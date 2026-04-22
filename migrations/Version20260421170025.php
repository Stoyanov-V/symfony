<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421170025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, name JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, restaurant_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_64C19C1B1E7706E ON category (restaurant_id)');
        $this->addSql('CREATE TABLE item (id UUID NOT NULL, name JSON NOT NULL, price NUMERIC(10, 2) NOT NULL, description JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, restaurant_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_1F1B251EB1E7706E ON item (restaurant_id)');
        $this->addSql('CREATE TABLE item_category (item_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY (item_id, category_id))');
        $this->addSql('CREATE INDEX IDX_6A41D10A126F525E ON item_category (item_id)');
        $this->addSql('CREATE INDEX IDX_6A41D10A12469DE2 ON item_category (category_id)');
        $this->addSql('CREATE TABLE restaurant (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE restaurant_user (restaurant_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (restaurant_id, user_id))');
        $this->addSql('CREATE INDEX IDX_4F85462DB1E7706E ON restaurant_user (restaurant_id)');
        $this->addSql('CREATE INDEX IDX_4F85462DA76ED395 ON restaurant_user (user_id)');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_6A41D10A126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_6A41D10A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_user ADD CONSTRAINT FK_4F85462DB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_user ADD CONSTRAINT FK_4F85462DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1B1E7706E');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251EB1E7706E');
        $this->addSql('ALTER TABLE item_category DROP CONSTRAINT FK_6A41D10A126F525E');
        $this->addSql('ALTER TABLE item_category DROP CONSTRAINT FK_6A41D10A12469DE2');
        $this->addSql('ALTER TABLE restaurant_user DROP CONSTRAINT FK_4F85462DB1E7706E');
        $this->addSql('ALTER TABLE restaurant_user DROP CONSTRAINT FK_4F85462DA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_category');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE restaurant_user');
        $this->addSql('DROP TABLE users');
    }
}
