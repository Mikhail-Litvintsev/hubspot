<?php

namespace App\DTO\Integrations\CustomBlocks;

class ClientPhoneDTO
{
    public function __construct(
        public readonly string $type = '',
        public readonly string $phone = '',
    ) {
    }
}