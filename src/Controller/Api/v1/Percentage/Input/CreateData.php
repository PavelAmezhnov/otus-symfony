<?php

namespace App\Controller\Api\v1\Percentage\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $taskId,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $skillId,
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(100)]
        public readonly float $percent
    ) {

    }
}
