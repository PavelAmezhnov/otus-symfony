<?php

namespace App\Consumer\CompletedTask\Update;

use App\Controller\Api\v1\CompletedTask\Input\UpdateData;
use JsonException;
use Symfony\Component\Validator\Constraints as Assert;

class Message
{

    #[Assert\GreaterThanOrEqual(1)]
    private int $id;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(10)]
    private ?int $grade = null;

    /**
     * @throws JsonException
     */
    public static function createFromQueue(string $messageBody): Message
    {
        $messageBody = json_decode($messageBody, true,512, JSON_THROW_ON_ERROR);
        $message = new self();
        $message->id = $messageBody['id'];
        $message->grade = $messageBody['grade'];

        return $message;
    }

    public function transformToDTO(): UpdateData
    {
        return new UpdateData(
            id: $this->getId(),
            grade: $this->getGrade()
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }
}
