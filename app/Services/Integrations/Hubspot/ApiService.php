<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\API\ApiClients\ApiClientInterface;

class ApiService
{
    public function __construct(
        protected readonly AuthService $authService
    ) {
    }

    /**
     * @param $user_id
     * @param $block_id
     * @return ApiClientInterface
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    protected function createApiClient($user_id, $block_id): ApiClientInterface
    {
        return app(ApiClientInterface::class,
            ['token' => $this->authService->getToken($user_id, $block_id)->access_token]
        );
    }
}