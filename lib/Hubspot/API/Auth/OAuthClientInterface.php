<?php

namespace UseDesk\Hubspot\API\Auth;

use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\API\DTO\TokenDTO;

interface OAuthClientInterface
{
    /**
     * @param string $clientId
     * @param string $redirectUrl
     * @param array $scopes
     * @param array $context
     *
     * @return string
     */
    public const REQUEST_OAUTH_TOKEN_URL = 'https://api.hubapi.com/oauth/v1/token';
    public function getAuthUrl(string $clientId, string $redirectUrl, array $scopes, array $context = []): string;

    /**
     * @param array $formParams
     *
     * @return TokenDTO
     *
     * @throws GuzzleException
     */
    public function requestToken(array $formParams): TokenDTO;
}
