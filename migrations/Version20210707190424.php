<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707190424 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE order_id_seq CASCADE');
        $this->addSql('CREATE TABLE example (id BIGSERIAL NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(500) NOT NULL, user_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_product (id INT NOT NULL, orders INT NOT NULL, product INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2530ADE6E52FFDEE ON order_product (orders)');
        $this->addSql('CREATE INDEX IDX_2530ADE6D34A04AD ON order_product (product)');
        $this->addSql('CREATE TABLE orders (id INT NOT NULL, user_id INT NOT NULL, amount INT NOT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, comment TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, title VARCHAR(255) NOT NULL, price INT NOT NULL, count INT NOT NULL, price_for_all INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6E52FFDEE FOREIGN KEY (orders) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6D34A04AD FOREIGN KEY (product) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE6E52FFDEE');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE6D34A04AD');
        $this->addSql('CREATE SEQUENCE order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE example');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE product');
    }
}
