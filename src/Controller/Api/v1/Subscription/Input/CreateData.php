<?php

namespace App\Controller\Api\v1\Subscription\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $studentId,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $courseId,
    ) {

    }
}
