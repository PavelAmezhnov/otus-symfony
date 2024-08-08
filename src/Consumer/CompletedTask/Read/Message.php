<?php

namespace App\Consumer\CompletedTask\Read;

use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use JsonException;
use Symfony\Component\Validator\Constraints as Assert;

class Message
{

    #[Assert\GreaterThanOrEqual(1)]
    private ?int $studentId = null;

    #[Assert\GreaterThanOrEqual(1)]
    private ?int $lessonId = null;

    #[Assert\GreaterThanOrEqual(1)]
    private ?int $taskId = null;

    #[Assert\GreaterThanOrEqual(1)]
    private ?int $skillId = null;

    #[Assert\GreaterThanOrEqual(1)]
    private ?int $courseId = null;

    #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
    private ?string $finishedAtGTE = null;

    #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
    private ?string $finishedAtLTE = null;

    /**
     * @throws JsonException
     */
    public static function createFromQueue(string $messageBody): Message
    {
        $messageBody = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);
        $message = new self();
        $message->studentId = $messageBody['studentId'] ?? null;
        $message->lessonId = $messageBody['lessonId'] ?? null;
        $message->taskId = $messageBody['taskId'] ?? null;
        $message->skillId = $messageBody['skillId'] ?? null;
        $message->courseId = $messageBody['courseId'] ?? null;
        $message->finishedAtGTE = $messageBody['finishedAtGTE'] ?? null;
        $message->finishedAtLTE = $messageBody['finishedAtLTE'] ?? null;

        return $message;
    }

    public function transformToDTO(): ReadData
    {
        return new ReadData(
            studentId: $this->getStudentId(),
            taskId: $this->getTaskId(),
            lessonId: $this->getLessonId(),
            skillId: $this->getSkillId(),
            courseId: $this->getCourseId(),
            finishedAtGTE: $this->getFinishedAtGTE(),
            finishedAtLTE: $this->getFinishedAtLTE()
        );
    }

    public function getStudentId(): ?int
    {
        return $this->studentId;
    }

    public function getLessonId(): ?int
    {
        return $this->lessonId;
    }

    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    public function getSkillId(): ?int
    {
        return $this->skillId;
    }

    public function getCourseId(): ?int
    {
        return $this->courseId;
    }

    public function getFinishedAtGTE(): ?string
    {
        return $this->finishedAtGTE;
    }

    public function getFinishedAtLTE(): ?string
    {
        return $this->finishedAtLTE;
    }
}
