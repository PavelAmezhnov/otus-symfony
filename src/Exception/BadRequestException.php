<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends Exception implements HttpCompliantExceptionInterface
{

    public function getHttpCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getHttpResponseBody(): string
    {
        return $this->getMessage();
    }
}
