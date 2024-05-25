<?php

namespace App\Controller\Api\v1\Achievement\Input;

enum SortEnum: string
{
    case RARITY = 'rarity';
    case DEFAULT = 'name';
}
