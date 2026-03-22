<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create appointment table for booking system';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE appointment (
            id INT AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            organization VARCHAR(255) DEFAULT NULL,
            service VARCHAR(255) NOT NULL,
            desired_date DATETIME NOT NULL,
            desired_time VARCHAR(5) NOT NULL,
            notes LONGTEXT DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            token VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            confirmed_at DATETIME DEFAULT NULL,
            rejected_at DATETIME DEFAULT NULL,
            rejection_reason LONGTEXT DEFAULT NULL,
            UNIQUE INDEX UNIQ_FE38F8445F37A13B (token),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appointment');
    }
}
