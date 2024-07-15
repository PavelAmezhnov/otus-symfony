<?php

namespace App\Controller\Api\v1\User\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\Length(min: 8, max: 32)]
        public readonly string $login,
        #[Assert\Length(min: 8, max: 128)]
        public readonly string $password
    ) {

    }
}
