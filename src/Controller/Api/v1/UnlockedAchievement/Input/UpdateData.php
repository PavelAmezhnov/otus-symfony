<?php

namespace App\Controller\Api\v1\UnlockedAchievement\Input;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateData
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public readonly int $id,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $studentId = null,
        #[Assert\GreaterThanOrEqual(1)]
        public readonly ?int $achievementId = null
    ) {
    }
}
