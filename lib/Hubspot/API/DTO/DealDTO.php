<?php

namespace UseDesk\Hubspot\API\DTO;

class DealDTO
{
    public function __construct(
        public readonly string $dealname,
        public readonly string $pipeline,
        public readonly string $dealstage,
        public readonly ?string $amount = null,
        public readonly ?int $hs_object_id = null,
        public readonly ?string $closedate = null,
        public readonly ?string $createdate = null,
        public readonly ?string $hs_lastmodifieddate = null,
        public readonly ?string $hubspot_owner_id = null,
        public readonly ?string $dealtype = null,
        public readonly ?string $hs_priority = null,
        public readonly ?string $pipelineId = null,
    ) {
    }
}