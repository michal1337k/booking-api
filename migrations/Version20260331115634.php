<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331115634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking CHANGE user_id user_id INT NOT NULL, CHANGE slot_id slot_id INT NOT NULL');
        $this->addSql('ALTER TABLE slot DROP duration, DROP is_booked, CHANGE date start_at DATE NOT NULL, CHANGE time end_at TIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking CHANGE user_id user_id INT DEFAULT NULL, CHANGE slot_id slot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE slot ADD duration INT NOT NULL, ADD is_booked TINYINT NOT NULL, CHANGE start_at date DATE NOT NULL, CHANGE end_at time TIME NOT NULL');
    }
}
