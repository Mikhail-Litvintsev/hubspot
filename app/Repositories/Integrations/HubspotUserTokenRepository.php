<?php

declare(strict_types=1);

namespace App\Repositories\Integrations;

use App\Models\Integrations\Hubspot\HubspotUserToken;
use Carbon\Carbon;
use UseDesk\Hubspot\API\Auth\TokenService;
use UseDesk\Hubspot\API\DTO\TokenDTO;

class HubspotUserTokenRepository
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param string $code
     * @return HubspotUserToken
     */
    public function saveCode(int $user_id, int $block_id, string $code): HubspotUserToken
    {
        return HubspotUserToken::updateOrCreate([
            'user_id' => $user_id,
            'block_id' => $block_id,
        ], [
            'code' => $code,
            'hubspot_user_token_dto' => null,
            'expire_at' => Carbon::now()
        ]);
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param TokenDTO $tokenDTO
     * @return HubspotUserToken
     */
    public function saveToken(int $user_id, int $block_id, TokenDTO $tokenDTO): HubspotUserToken
    {
        return HubspotUserToken::updateOrCreate([
            'user_id' => $user_id,
            'block_id' => $block_id,
        ], [
            'hubspot_user_token_dto' => $this->tokenService->encodeToken($tokenDTO),
            'expire_at' => Carbon::now()->addSeconds($tokenDTO->expires_in)->toDateTimeString(),
        ]);
    }
}