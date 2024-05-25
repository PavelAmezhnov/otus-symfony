<?php

namespace App\Controller\Api\v1\Common;

use App\Collection\EntityCollection;
use App\Entity\HasArrayRepresentation;

class OkResult
{

    use ResultTrait;

    public function __construct(private readonly mixed $payload)
    {
        $this->setSuccess(true);
    }

    public function getData(): mixed
    {
        if ($this->payload instanceof HasArrayRepresentation) {
            $data = $this->payload->toArray();
        } elseif ($this->payload instanceof EntityCollection) {
            $data = array_map(static fn(HasArrayRepresentation $i) => $i->toArray(), $this->payload->toArray());
        } else {
            $data = $this->payload;
        }

        return $data;
    }
}
