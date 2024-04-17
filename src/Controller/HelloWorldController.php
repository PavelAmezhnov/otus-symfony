<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\CompletedTask;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Entity\Task;
use App\Entity\UnlockedAchievement;
use App\Manager\AchievementManager;
use App\Manager\CompletedTaskManager;
use App\Manager\CourseManager;
use App\Manager\LessonManager;
use App\Manager\PercentageManager;
use App\Manager\SkillManager;
use App\Manager\StudentManager;
use App\Manager\SubscriptionManager;
use App\Manager\TaskManager;
use App\Manager\UnlockedAchievementManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class HelloWorldController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    #[Route('/student/create')]
    public function createStudent(Request $request, StudentManager $studentManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $studentManager->create($firstName, $lastName);
        $result = [];

        /** @var Student $s */
        foreach ($this->entityManager->getRepository(Student::class)->findByName($firstName)->toArray() as $s) {
            $result[] = $s->toArray();
        }

        return $this->json($result);
    }

    #[Route('/course/create')]
    public function createCourse(Request $request, CourseManager $courseManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $courseManager->create($name);
        $result = [];

        /** @var Course $c */
        foreach ($this->entityManager->getRepository(Course::class)->findByName($name)->toArray() as $c) {
            $result[] = $c->toArray();
        }

        return $this->json($result);
    }

    #[Route('/lesson/create')]
    public function createLesson(Request $request, LessonManager $lessonManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $course = $this->entityManager->getRepository(Course::class)->findOneBy(['id' => $courseId]);
        $lessonManager->create($name, $course);
        $result = [];

        /** @var Lesson $l */
        foreach ($this->entityManager->getRepository(Lesson::class)->findByName($name)->toArray() as $l) {
            $result[] = $l->toArray();
        }

        return $this->json($result);
    }

    #[Route('/task/create')]
    public function createTask(Request $request, TaskManager $taskManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $lesson = $this->entityManager->getRepository(Lesson::class)->findOneBy(['id' => $lessonId]);
        $taskManager->create($name, $lesson);
        $result = [];

        /** @var Task $t */
        foreach ($this->entityManager->getRepository(Task::class)->findBy(['name' => $name]) as $t) {
            $result[] = $t->toArray();
        }

        return $this->json($result);
    }

    #[Route('/skill/create')]
    public function createSkill(Request $request, SkillManager $skillManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $skillManager->create($name);
        $result = [];

        /** @var Skill $s */
        foreach ($this->entityManager->getRepository(Skill::class)->findByName($name)->toArray() as $s) {
            $result[] = $s->toArray();
        }

        return $this->json($result);
    }

    #[Route('/subscription/subscribe')]
    public function subscribe(Request $request, SubscriptionManager $subscriptionManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        $subscriptionManager->subscribe($student, $course);
        $result = [];
        $repo = $this->entityManager->getRepository(Subscription::class);

        /** @var Subscription $s */
        foreach ($repo->findBy(['student' => $student, 'course' => $course]) as $s) {
            $result[] = $s->toArray();
        }

        return $this->json($result);
    }

    #[Route('/achievement/create')]
    public function createAchievement(Request $request, AchievementManager $achievementManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $achievementManager->create($name);
        $result = [];

        /** @var Achievement $a */
        foreach ($this->entityManager->getRepository(Achievement::class)->findBy(['name' => $name]) as $a) {
            $result[] = $a->toArray();
        }

        return $this->json($result);
    }

    #[Route('/unlocked-achievement/unlock')]
    public function unlockAchievement(Request $request, UnlockedAchievementManager $unlockedAchievementManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $achievement = $this->entityManager->getRepository(Achievement::class)->find($achievementId);
        $unlockedAchievementManager->give($student, $achievement);
        $repo = $this->entityManager->getRepository(UnlockedAchievement::class);
        $result = [];

        /** @var UnlockedAchievement $ua */
        foreach ($repo->findBy(['student' => $student, 'achievement' => $achievement]) as $ua) {
            $result[] = $ua->toArray();
        }

        return $this->json($result);
    }

    #[Route('/completed-task/create')]
    public function createCompletedTask(Request $request, CompletedTaskManager $completedTaskManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $completedTaskManager->create($student, $task);
        $repo = $this->entityManager->getRepository(CompletedTask::class);
        $result = [];

        /** @var CompletedTask $ct */
        foreach ($repo->findBy(['student' => $student, 'task' => $task]) as $ct) {
            $result[] = $ct->toArray();
        }

        return $this->json($result);
    }

    #[Route('/completed-task/rate')]
    public function rate(Request $request, CompletedTaskManager $completedTaskManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        /** @var CompletedTask $completedTask */
        $completedTask = $this->entityManager->getRepository(CompletedTask::class)->find($completedTaskId);
        $completedTaskManager->rate($completedTask, $rate);

        return $this->json($completedTask->toArray());
    }

    /**
     * @throws Exception
     */
    #[Route('/percentage/create')]
    public function createPercentage(Request $request, PercentageManager $percentageManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $skill = $this->entityManager->getRepository(Skill::class)->find($skillId);
        $percentageManager->create($task, $skill, $percent);
        $result = [];

        /** @var Percentage $p */
        foreach ($this->entityManager->getRepository(Percentage::class)->findBy(['task' => $task]) as $p) {
            $result[] = $p->toArray();
        }

        return $this->json($result);
    }

    #[Route('/lesson/change-course')]
    public function changeCourse(Request $request, LessonManager $lessonManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($lessonId);
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        $lessonManager->changeCourse($lesson, $course);

        return $this->json($lesson->toArray());
    }

    #[Route('/task/change-lesson')]
    public function changeLesson(Request $request, TaskManager $taskManager): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($lessonId);
        $taskManager->changeLesson($task, $lesson);

        return $this->json($task->toArray());
    }

    #[Route('/achievement/get-sorted-by-rarity-achievement-list')]
    public function getSortedByRarityAchievementList(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $repo = $this->entityManager->getRepository(Achievement::class);
        $result = array_map(
            static fn($item) => $item->toArray(),
            $repo->getSortedByRarityAchievementList()
        );

        return $this->json($result);
    }

    #[Route('/achievement/get-count-students-with-achievement-in-percentage')]
    public function getCountStudentsWithAchievementInPercentage(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $repo = $this->entityManager->getRepository(Achievement::class);
        $result = $repo->getCountStudentsWithAchievementInPercentage($repo->find($achievementId));

        return $this->json($result);
    }

    #[Route('/completed-task/get-total-grade-for-course')]
    public function getTotalGradeForCourse(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $repo = $this->entityManager->getRepository(CompletedTask::class);
        $result = $repo->getTotalGradeForCourse($course, $student);

        return $this->json($result);
    }

    #[Route('/completed-task/get-total-grade-for-lesson')]
    public function getTotalGradeForLesson(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($lessonId);
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $repo = $this->entityManager->getRepository(CompletedTask::class);
        $result = $repo->getTotalGradeForLesson($lesson, $student);

        return $this->json($result);
    }

    #[Route('/completed-task/get-total-grade-for-skill')]
    public function getTotalGradeForSkill(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $skill = $this->entityManager->getRepository(Skill::class)->find($skillId);
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $repo = $this->entityManager->getRepository(CompletedTask::class);
        $result = $repo->getTotalGradeForSkill($skill, $student);

        return $this->json($result);
    }

    /**
     * @throws Exception
     */
    #[Route('/completed-task/get-total-grade-in-time-range')]
    public function getTotalGradeInTimeRange(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        $repo = $this->entityManager->getRepository(CompletedTask::class);
        $result = $repo->getTotalGradeInTimeRange($startDate, $endDate, $student);

        return $this->json($result);
    }

    #[Route('/course/get-subscribed-students')]
    public function getSubscribedStudents(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $repo = $this->entityManager->getRepository(Course::class);
        $course = $repo->find($courseId);

        $result = array_map(
            static fn(Student $s) => $s->toArray(),
            $repo->getSubscribedStudents($course)->toArray()
        );

        return $this->json($result);
    }

    #[Route('/task/get-tasks-with-specific-skill')]
    public function getTasksWithSpecificSkill(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

        $result = array_map(
            static fn(Task $t) => $t->toArray(),
            $this->entityManager->getRepository(Task::class)->getTasksWithSpecificSkill($skill)->toArray()
        );

        return $this->json($result);
    }

    #[Route('/student/get-achievements')]
    public function getAchievements(Request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $repo = $this->entityManager->getRepository(Student::class);
        $student = $repo->find($studentId);

        $result = array_map(
            static fn(Achievement $a) => $a->toArray(),
            $repo->getAchievements($student)->toArray()
        );

        return $this->json($result);
    }

    #[Route('/student/find-by-name')]
    public function getSome(request $request): JsonResponse
    {
        extract(json_decode($request->getContent(), true));
        $repo = $this->entityManager->getRepository(Student::class);
        $result = array_map(
            static fn(Student $s) => $s->toArray(),
            $repo->findByName($name)
        );

        return $this->json($result);
    }
}
