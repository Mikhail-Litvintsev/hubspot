<?php

namespace UseDesk\Hubspot\API\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\API\DTO\TokenDTO;
use UseDesk\Hubspot\Book\Book;

class OAuthClient implements OAuthClientInterface
{
    /**
     * Ссылка для OAuth с запросом необходимых разрешений
     *
     * @param string $clientId
     * @param string $redirectUrl
     * @param array $scopes
     * @param array $context
     * @return string
     */
    public function getAuthUrl(string $clientId, string $redirectUrl, array $scopes, array $context = []): string
    {
        $url = Book::OAUTH_URL . '?';
        $url .= Book::CLIENT_ID . '=' . $clientId;
        $url .= '&' . Book::REDIRECT_URI . '=' . $redirectUrl;
        $url .= '&' . Book::SCOPE . '=' . implode(Book::SCOPE_SEPARATOR, $scopes);
        $url .= '&' . Book::STATE . '=' . json_encode($context);

        return $url;
    }

    /**
     * Запрос токена в Hubspot
     *
     * @param array $formParams
     * @return TokenDTO
     * @throws GuzzleException
     */
    public function requestToken(array $formParams): TokenDTO
    {
        $client = app(Client::class);
        $response = $client->post(self::REQUEST_OAUTH_TOKEN_URL, ['form_params' => $formParams]);
        $response = json_decode($response->getBody()->getContents(), true);
        return new TokenDTO(...$response);
    }
}