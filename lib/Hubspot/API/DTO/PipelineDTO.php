<?php

namespace UseDesk\Hubspot\API\DTO;

class PipelineDTO
{
    /** @var StageDTO [] */
    public readonly array $stages;

    public function __construct(
        public readonly ?string $label,
        public readonly ?int $displayOrder,
        public readonly ?bool $active,
        public readonly ?string $objectType,
        public readonly ?string $objectTypeId,
        public readonly ?string $pipelineId,
        public readonly ?int $createdAt,
        public readonly ?int $updatedAt,
        public readonly ?bool $default,
        array $stages = []
    ) {
        $stagesDTO = [];
        foreach ($stages as $stage) {
            $stagesDTO[] = new StageDTO(...$stage);
        }
        $this->stages = $stagesDTO;
    }
}