<?php

namespace App\Consumer\CompletedTask\Read;

use App\Collection\EntityCollection;
use App\Consumer\RejectTrait;
use App\Service\CompletedTaskService;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class Consumer implements ConsumerInterface
{

    use RejectTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CompletedTaskService $completedTaskService
    ){

    }

    public function execute(AMQPMessage $msg): bool|int
    {
        try {
            var_dump($msg->getBody());

            $message = Message::createFromQueue($msg->getBody());
            $errors = $this->validator->validate($message);
            if ($errors->count() > 0) {
                throw new Exception((string) $errors);
            }

            $this->completedTaskService->read($message->transformToDTO());
        } catch (Throwable $e) {
            return $this->reject($e->getMessage());
        }

        return $this::MSG_ACK;
    }
}
