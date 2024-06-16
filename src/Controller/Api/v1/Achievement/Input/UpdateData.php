<?php

namespace App\Controller\Api\v1\Achievement\Input;

use App\Entity\Achievement;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\Length(min: 3, max: 128)]
        public string $name,
    ) {
    }

    public static function fromEntity(Achievement $achievement): self
    {
        return new self(
            id: $achievement->getId(),
            name: $achievement->getName()
        );
    }
}
