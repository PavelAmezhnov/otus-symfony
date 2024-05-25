<?php

namespace App\Controller\Api\v1\Common;

class BadRequestResult
{

    use ResultTrait;

    public function __construct(string $message)
    {
        $this->setSuccess(false);
        $this->setMessage($message);
    }
}
