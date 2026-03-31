<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203171223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD request_type VARCHAR(50) DEFAULT NULL, ADD appointment_date DATETIME DEFAULT NULL, ADD appointment_end_date DATETIME DEFAULT NULL, ADD appointment_status VARCHAR(50) DEFAULT NULL, ADD microsoft_event_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP request_type, DROP appointment_date, DROP appointment_end_date, DROP appointment_status, DROP microsoft_event_id');
    }
}
