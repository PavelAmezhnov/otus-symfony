<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514165749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX achievement__name__uniq ON achievement (name)');
        $this->addSql('CREATE UNIQUE INDEX completed_task__student__task__uniq ON completed_task (student_id, task_id)');
        $this->addSql('CREATE UNIQUE INDEX course__name__uniq ON course (name)');
        $this->addSql('CREATE UNIQUE INDEX lesson__name__course__uniq ON lesson (name, course_id)');
        $this->addSql('CREATE UNIQUE INDEX percentage__task__skill__uniq ON percentage (task_id, skill_id)');
        $this->addSql('CREATE UNIQUE INDEX skill__name__uniq ON skill (name)');
        $this->addSql('CREATE UNIQUE INDEX subscription__student__course__uniq ON subscription (student_id, course_id)');
        $this->addSql('CREATE UNIQUE INDEX task__name__lesson__uniq ON task (name, lesson_id)');
        $this->addSql('CREATE UNIQUE INDEX unlocked_achievement__student__achievement__uniq ON unlocked_achievement (student_id, achievement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX lesson__name__course__uniq');
        $this->addSql('DROP INDEX completed_task__student__task__uniq');
        $this->addSql('DROP INDEX course__name__uniq');
        $this->addSql('DROP INDEX task__name__lesson__uniq');
        $this->addSql('DROP INDEX subscription__student__course__uniq');
        $this->addSql('DROP INDEX skill__name__uniq');
        $this->addSql('DROP INDEX achievement__name__uniq');
        $this->addSql('DROP INDEX unlocked_achievement__student__achievement__uniq');
        $this->addSql('DROP INDEX percentage__task__skill__uniq');
    }
}
