<?php

namespace App\Controller\Api\v1\Task\Input;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\Length(min: 3, max: 128)]
        public readonly ?string $name = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $lessonId = null
    ) {
    }
}
