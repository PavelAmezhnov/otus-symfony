<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class EntityNotFoundException extends Exception implements HttpCompliantExceptionInterface
{

    public function getHttpCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getHttpResponseBody(): string
    {
        return empty($this->getMessage()) ? 'Entity not found' : $this->getMessage();
    }
}
