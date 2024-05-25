<?php

namespace App\Controller\Api\v1\Common;

class NoContentResult
{

    use ResultTrait;

    public function __construct()
    {
        $this->setSuccess(true);
    }

    public function getData(): array
    {
        return [];
    }
}
