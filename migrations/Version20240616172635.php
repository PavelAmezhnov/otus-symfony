<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240616172635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE curated_course (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, teacher_id INT DEFAULT NULL, course_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX curated_course__teacher_id__ind ON curated_course (teacher_id)');
        $this->addSql('CREATE INDEX curated_course__course_id__ind ON curated_course (course_id)');
        $this->addSql('CREATE UNIQUE INDEX curated_course__teacher__course__uniq ON curated_course (teacher_id, course_id)');
        $this->addSql('CREATE TABLE teacher (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX teacher__last_name__first_name__ind ON teacher (last_name, first_name)');
        $this->addSql('CREATE INDEX teacher__first_name__last_name__ind ON teacher (first_name, last_name)');
        $this->addSql('CREATE UNIQUE INDEX teacher__user__uniq ON teacher (user_id)');
        $this->addSql('CREATE TABLE "user" (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, login VARCHAR(128) NOT NULL, password VARCHAR(128) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX user__login__uniq ON "user" (login)');
        $this->addSql('ALTER TABLE curated_course ADD CONSTRAINT curated_course__teacher_id__fk FOREIGN KEY (teacher_id) REFERENCES teacher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE curated_course ADD CONSTRAINT curated_course__course_id__fk FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT teacher__user_id__fk FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT student__user_id__fk FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX student__user__uniq ON student (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE curated_course DROP CONSTRAINT curated_course__teacher_id__fk');
        $this->addSql('ALTER TABLE curated_course DROP CONSTRAINT curated_course__course_id__fk');
        $this->addSql('ALTER TABLE teacher DROP CONSTRAINT teacher__user_id__fk');
        $this->addSql('DROP TABLE curated_course');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT student__user_id__fk');
        $this->addSql('DROP INDEX student__user__uniq');
        $this->addSql('ALTER TABLE student DROP user_id');
    }
}
