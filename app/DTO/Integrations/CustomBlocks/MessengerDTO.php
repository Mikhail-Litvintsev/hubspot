<?php

namespace App\DTO\Integrations\CustomBlocks;

class MessengerDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $id = null,
    ) {
    }
}