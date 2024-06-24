<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidTokenException extends Exception implements HttpCompliantExceptionInterface
{

    public function getHttpCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }

    public function getHttpResponseBody(): string
    {
        return $this->getMessage();
    }
}
