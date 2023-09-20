<?php

namespace UseDesk\Hubspot\API\DTO;

class TokenDTO
{
    public function __construct(
        readonly public string $token_type = 'bearer',
        readonly public string $refresh_token,
        readonly public string $access_token,
        readonly int $expires_in,
    ) {
    }
}