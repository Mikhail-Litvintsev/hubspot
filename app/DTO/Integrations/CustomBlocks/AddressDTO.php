<?php

namespace App\DTO\Integrations\CustomBlocks;

class AddressDTO
{
    public function __construct(
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?string $address = null,
        public readonly ?string $type = null,
    ) {
    }
}