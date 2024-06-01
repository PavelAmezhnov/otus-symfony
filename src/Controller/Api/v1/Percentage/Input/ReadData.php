<?php

namespace App\Controller\Api\v1\Percentage\Input;

use Symfony\Component\Validator\Constraints as Assert;

class ReadData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $taskId,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $page = 1,
        #[Assert\Range(min: 1, max: 1000)]
        public readonly int $perPage = 100,
    ) {
    }
}
