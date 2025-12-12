<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211154627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT NOT NULL, is_archived TINYINT NOT NULL, created_at DATETIME NOT NULL, replied_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, organization VARCHAR(255) DEFAULT NULL, subscribed_to_newsletter TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service_pricing (id INT AUTO_INCREMENT NOT NULL, service_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, unit_price NUMERIC(10, 2) DEFAULT NULL, daily_pricing JSON DEFAULT NULL, pricing_unit VARCHAR(50) DEFAULT NULL, is_active TINYINT NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE testimony (id INT AUTO_INCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, position VARCHAR(255) DEFAULT NULL, organization VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, rating INT NOT NULL, image_path VARCHAR(255) DEFAULT NULL, is_published TINYINT NOT NULL, display_order INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(10) NOT NULL, start_time VARCHAR(5) NOT NULL, end_time VARCHAR(5) NOT NULL, is_available TINYINT NOT NULL, notes VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE webinar (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, link VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE service_pricing');
        $this->addSql('DROP TABLE testimony');
        $this->addSql('DROP TABLE timetable');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE webinar');
    }
}
