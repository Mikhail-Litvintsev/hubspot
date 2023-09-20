<?php

namespace UseDesk\Hubspot\API\DTO;

class OwnerDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $userId,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly bool $archived = false,
        public readonly ?string $teams = null,
    ) {
    }
}