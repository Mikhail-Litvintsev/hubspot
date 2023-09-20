<?php

namespace App\VO\Integrations\Hubspot;

class ContactVO extends HubspotEntityVO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $firstname = null,
        public readonly ?string $lastname = null,
        public readonly ?string $phone = null,
        public readonly ?string $company = null,
        public readonly ?string $website = null,
        public readonly ?string $hs_object_id = null,
    ) {
    }
}