<?php

namespace App\Entity;

interface HasArrayRepresentation
{

    public function toArray(): array;
}
