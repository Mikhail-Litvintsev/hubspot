<?php

namespace App\VO\Integrations\Hubspot;

class OwnerVO extends HubspotEntityVO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
    ) {
    }
}