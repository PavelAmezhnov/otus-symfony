<?php

namespace App\EventListener;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Common\NoContentResult;
use App\Controller\Api\v1\Common\OkResult;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class KernelViewEventListener
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function onKernelView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();

        if ($value instanceof EntityCollection) {
            if ($value->isEmpty()) {
                $event->setResponse($this->getHttpResponse(new NoContentResult(), Response::HTTP_NO_CONTENT));
            } else {
                $event->setResponse($this->getHttpResponse(new OkResult($value), Response::HTTP_OK));
            }
        }

        $event->setResponse($this->getHttpResponse(new OkResult($value), Response::HTTP_OK));
    }

    private function getHttpResponse(mixed $result, int $code): Response
    {
        $responseData = $this->serializer->serialize(
            $result,
            JsonEncoder::FORMAT,
            [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
        );

        return new Response($responseData, $code, ['Content-Type' => 'application/json']);
    }
}
