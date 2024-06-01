<?php

namespace App\Controller\Api\v1\CompletedTask\Input;

use Symfony\Component\Validator\Constraints as Assert;

class ReadData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $page = 1,
        #[Assert\Range(min: 1, max: 1000)]
        public readonly int $perPage = 100,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $studentId = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $taskId = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $lessonId = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $skillId = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $courseId = null,
        #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
        public readonly ?string $finishedAtGTE = null,
        #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
        public readonly ?string $finishedAtLTE = null
    ) {

    }
}
