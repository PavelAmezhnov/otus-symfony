<?php

namespace App\Controller\Api\v1\Student\Input;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\Length(min: 3, max: 64)]
        public readonly string $firstName,
        #[Assert\Length(min: 3, max: 64)]
        public readonly ?string $lastName = null,
    ) {
    }
}
