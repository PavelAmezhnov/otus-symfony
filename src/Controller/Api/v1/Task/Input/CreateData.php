<?php

namespace App\Controller\Api\v1\Task\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\Length(min: 3, max: 128)]
        public readonly string $name,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $lessonId
    ) {

    }
}
