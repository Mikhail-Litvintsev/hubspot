<?php

namespace UseDesk\Hubspot\API\DTO;

class StageDTO
{
    public readonly ?StagesMetaDataDTO $metadata;

    public function __construct(
        public readonly ?string $label,
        public readonly ?int $displayOrder,
        array $metadata,
        public readonly ?string $stageId,
        public readonly ?int $createdAt,
        public readonly ?int $updatedAt,
        public readonly ?bool $active
    ) {
        $this->metadata = new StagesMetaDataDTO(...$metadata);
    }
}