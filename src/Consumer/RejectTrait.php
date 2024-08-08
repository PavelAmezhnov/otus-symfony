<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

trait RejectTrait
{

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return ConsumerInterface::MSG_REJECT;
    }
}
