<?php

namespace App\Consumer\Common\Invalidate;

use JsonException;

class Message
{

    private array $tags;

    /**
     * @throws JsonException
     */
    public static function createFromQueue(string $messageBody): Message
    {
        $messageBody = json_decode($messageBody, true,512, JSON_THROW_ON_ERROR);
        $message = new self();
        $message->tags = $messageBody['tags'];

        return $message;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
