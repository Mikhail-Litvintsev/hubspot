<?php

namespace App\DTO\Integrations\CustomBlocks;

class SocialServiceDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $url = null,
        public readonly ?string $uid = null,
    ) {
    }
}