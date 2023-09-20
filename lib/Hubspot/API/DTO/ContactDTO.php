<?php

namespace UseDesk\Hubspot\API\DTO;

class ContactDTO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $firstname = null,
        public readonly ?string $lastname = null,
        public readonly ?string $phone = null,
        public readonly ?string $company = null,
        public readonly ?string $website = null,
        public readonly ?string $lifecyclestage = null,
        public readonly ?string $createdate = null,
        public readonly ?string $hs_email_domain = null,
        public readonly ?int $hs_object_id = null,
        public readonly ?string $hs_object_source_id = null,
        public readonly ?string $hs_pipeline = null,
        public readonly ?string $lastmodifieddate = null,
    ) {
    }
}