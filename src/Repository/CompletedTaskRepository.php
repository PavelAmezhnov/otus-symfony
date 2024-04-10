<?php

namespace App\Repository;

use App\Entity\CompletedTask;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Student;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class CompletedTaskRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompletedTask::class);
    }

    /**
     * Возвращает полученный студентом суммарный балл за все задания урока
     *
     * @param Lesson $lesson
     * @param Student $student
     * @return int
     */
    public function getTotalGradeForLesson(Lesson $lesson, Student $student): int
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('student', $student))
            ->andWhere(Criteria::expr()->contains('task', $lesson->getTasks()));
        $completedTasks = $this->matching($criteria);

        $totalGrade = 0;
        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            $totalGrade += $completedTask->getGrade();
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем выполненным заданиям, включающим указанный навыка
     *
     * @param Skill $skill
     * @param Student $student
     * @return float
     */
    public function getTotalGradeForSkill(Skill $skill, Student $student): float
    {
        $completedTasks = $this->findBy(['student' => $student]);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            /** @var Percentage $percentage */
            foreach ($completedTask->getTask()->getPercentages() as $percentage) {
                if ($percentage->getSkill() === $skill) {
                    $totalGrade += 0.01 * $percentage->getPercent() * $completedTask->getGrade();
                }
            }
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем пополненным заданиям указанного курса
     *
     * @param Course $course
     * @param Student $student
     * @return int
     */
    public function getTotalGradeForCourse(Course $course, Student $student): int
    {
        $completedTasks = $this->findBy(['student' => $student]);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            if ($completedTask->getTask()->getLesson()->getCourse() === $course) {
                $totalGrade += $completedTask->getGrade();
            }
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем пополненным заданиям за указанный интервал времени
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param Student $student
     * @return int
     */
    public function getTotalGradeInTimeRange(DateTime $startDate, DateTime $endDate, Student $student): int
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('student', $student))
            ->andWhere(Criteria::expr()->gte('finishedAt', $startDate))
            ->andWhere(Criteria::expr()->lte('finishedAt', $endDate));
        $completedTasks = $this->matching($criteria);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            $totalGrade += $completedTask->getGrade();
        }

        return $totalGrade;
    }
}
