<?php

namespace App\Controller\Api\v1\Achievement\Input;

use Symfony\Component\Validator\Constraints as Assert;

class ReadData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $page = 1,
        #[Assert\Range(min: 1, max: 1000)]
        public readonly int $perPage = 100,
        #[Assert\Length(min: 3, max: 128)]
        public readonly ?string $name = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $studentId = null,
        #[Assert\Type(type: SortEnum::class)]
        public readonly SortEnum $sort = SortEnum::DEFAULT
    ) {
    }
}
