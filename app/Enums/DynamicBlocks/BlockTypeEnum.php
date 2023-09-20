<?php

declare(strict_types=1);

namespace App\Enums\DynamicBlocks;

enum BlockTypeEnum: string
{
    case EXTERNAL = 'external';
    case HUBSPOT = 'hubspot';
}
