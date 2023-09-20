<?php

namespace UseDesk\Hubspot\API\DTO;

class StagesMetaDataDTO
{
    public function __construct(
        public readonly ?string $isClosed,
        public readonly ?string $probability
    ) {
    }
}