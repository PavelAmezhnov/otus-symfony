<?php

namespace App\Controller\Api\v1\CompletedTask\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $studentId,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $taskId,
        #[Assert\GreaterThanOrEqual(1)]
        #[Assert\LessThanOrEqual(10)]
        public readonly ?int $grade = null
    ) {

    }

    public static function fromFormData(array $data): self
    {
        return new self(
            studentId: $data['student']->getId(),
            taskId: $data['task']->getid(),
            grade: $data['grade']
        );
    }
}
