<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210328211424 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE example (id BIGSERIAL NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(500) NOT NULL, user_id BIGINT DEFAULT NULL, PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        throw new \Exception('Not possible');
    }
}
