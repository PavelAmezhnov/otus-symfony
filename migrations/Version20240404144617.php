<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404144617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE unlocked_achievement (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, student_id INT DEFAULT NULL, achievement_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX unlocked_achievement__student_id__ind ON unlocked_achievement (student_id)');
        $this->addSql('CREATE INDEX unlocked_achievement__achievement_id__ind ON unlocked_achievement (achievement_id)');
        $this->addSql('ALTER TABLE unlocked_achievement ADD CONSTRAINT unlocked_achievement__student_id__fk FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unlocked_achievement ADD CONSTRAINT unlocked_achievement__achievement_id__fk FOREIGN KEY (achievement_id) REFERENCES achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE completed_task ADD finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE completed_task ALTER grade DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unlocked_achievement DROP CONSTRAINT unlocked_achievement__student_id__fk');
        $this->addSql('ALTER TABLE unlocked_achievement DROP CONSTRAINT unlocked_achievement__achievement_id__fk');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE unlocked_achievement');
        $this->addSql('ALTER TABLE completed_task DROP finished_at');
        $this->addSql('ALTER TABLE completed_task ALTER grade SET NOT NULL');
    }
}
