<?php

namespace App\DTO\Integrations\CustomBlocks;

class ChannelDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $data = null,
        public readonly ?int $id = null,
    ) {
    }
}