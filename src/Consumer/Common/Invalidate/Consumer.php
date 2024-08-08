<?php

namespace App\Consumer\Common\Invalidate;

use App\Consumer\RejectTrait;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

class Consumer implements ConsumerInterface
{

    use RejectTrait;

    public function __construct(
        private readonly TagAwareCacheInterface $cache
    ) {

    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $message = Message::createFromQueue($msg->getBody());
            $this->cache->invalidateTags($message->getTags());
        } catch (Throwable $e) {
            return $this->reject($e->getMessage());
        }

        return $this::MSG_ACK;
    }
}
