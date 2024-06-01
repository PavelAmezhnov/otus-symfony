<?php

namespace App\Controller\Api\v1\Lesson\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $courseId,
        #[Assert\Length(min: 3, max: 128)]
        public readonly string $name
    ) {

    }
}
