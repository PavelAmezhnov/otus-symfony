<?php

namespace App\Controller\Api\v1\Common;

trait ResultTrait
{
    private bool $success;

    private ?string $message = null;

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
