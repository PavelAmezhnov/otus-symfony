<?php

namespace App\Controller\Api\v1\Subscription\Input;

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
        public readonly ?int $courseId = null
    ) {
    }
}
