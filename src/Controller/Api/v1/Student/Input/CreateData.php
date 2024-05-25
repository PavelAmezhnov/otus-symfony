<?php

namespace App\Controller\Api\v1\Student\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\Length(min: 3, max: 64)]
        public readonly string $firstName,
        #[Assert\Length(min: 3, max: 64)]
        public readonly ?string $lastName = null
    ) {

    }
}
