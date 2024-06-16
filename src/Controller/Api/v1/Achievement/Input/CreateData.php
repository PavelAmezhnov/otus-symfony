<?php

namespace App\Controller\Api\v1\Achievement\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateData
{

    public function __construct(
        #[Assert\Length(min: 3, max: 128)]
        public readonly string $name
    ) {

    }

    public static function fromFormData(array $data): self
    {
        return new self(
            name: $data['name']
        );
    }
}
