<?php

namespace App\Controller\Api\v1\CompletedTask\Input;

use App\Entity\CompletedTask;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\GreaterThanOrEqual(1)]
        #[Assert\LessThanOrEqual(10)]
        public ?int $grade = null
    ) {
    }

    public static function fromEntity(CompletedTask $completedTask): self
    {
        return new self(
            id: $completedTask->getId(),
            grade: $completedTask->getGrade()
        );
    }
}
