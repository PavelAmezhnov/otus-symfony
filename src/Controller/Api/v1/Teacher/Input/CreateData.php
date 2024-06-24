<?php

namespace App\Controller\Api\v1\Teacher\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\NotBlank]
        public readonly string $userLogin,
        #[Assert\Length(min: 3, max: 64)]
        public readonly string $firstName,
        #[Assert\Length(min: 3, max: 64)]
        public readonly ?string $lastName = null
    ) {

    }
}
