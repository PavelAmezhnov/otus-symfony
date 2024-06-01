<?php

namespace App\Controller\Api\v1\Percentage\Input;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(100)]
        public readonly float $percent
    ) {
    }
}
