<?php

namespace App\VO\Integrations\Hubspot;

class CommentVO extends HubspotEntityVO
{
    public function __construct(
        public readonly string $hs_object_id,
        public readonly string $hs_note_body,
        public readonly string $hs_createdate,
        public readonly ?string $hubspot_owner_id,
        public readonly ?string $hs_lastmodifieddate,
        public readonly ?string $hs_attachment_ids,
    ) {
    }
}