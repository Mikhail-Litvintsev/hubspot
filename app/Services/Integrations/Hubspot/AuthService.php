<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Models\Integrations\Hubspot\HubspotUserToken;
use App\Repositories\Integrations\HubspotUserTokenRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\URL;
use UseDesk\Hubspot\API\Auth\OAuthClientInterface;
use UseDesk\Hubspot\API\Auth\TokenService;
use UseDesk\Hubspot\API\DTO\TokenDTO;

class AuthService
{
    public const USER_ID = 'user_id';
    public const BLOCK_ID = 'block_id';

    /**
     * @param TokenService $tokenService
     * @param OAuthClientInterface $authClient
     * @param HubspotSettingsService $settingsService
     * @param HubspotUserTokenRepository $userTokenRepository
     */
    public function __construct(
        protected TokenService $tokenService,
        protected OAuthClientInterface $authClient,
        protected HubspotSettingsService $settingsService,
        protected HubspotUserTokenRepository $userTokenRepository
    )
    {
    }

    /**
     * Получение / обновление токена, есл пользователь дал права
     *
     * @param int $user_id
     * @param int $block_id
     * @return TokenDTO
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getToken(int $user_id, int $block_id): TokenDTO
    {
        $dbToken = HubspotUserToken::where(['user_id' => $user_id, 'block_id' => $block_id])->first();

        if ($dbToken?->id === null) {
            throw new UserNotAuthenticatedException();
        }

        if ($dbToken->hubspot_user_token_dto === null) {
            $dbToken = $this->createNewToken($user_id, $block_id, $dbToken->code);
        }

        if ($dbToken->isValid() === false) {
            $dbToken = $this->refreshToken(
                $user_id,
                $block_id,
                $this->tokenService->decodeToken($dbToken->hubspot_user_token_dto),
            );
        }
        return $this->tokenService->decodeToken($dbToken->hubspot_user_token_dto);
    }

    /**
     * Ссылка для OAuth авторизации
     *
     * @param int $user_id
     * @param int $block_id
     * @return string
     */
    public function getOAuthUrl(int $user_id, int $block_id): string
    {
        $client_id = config('hubspot.client_id');
        $redirectUrl = $this->getRedirectUrl();
        $scopes = $this->settingsService->getScopes();
        $context = [
            self::USER_ID => $user_id,
            self::BLOCK_ID => $block_id
        ];

        return $this->authClient->getAuthUrl($client_id, $redirectUrl, $scopes, $context);
    }

    /**
     * Сохранение кода для получения токена, после выдачи всех разрешений пользователем
     *
     * @param int $user_id
     * @param int $block_id
     * @param string $code
     * @return HubspotUserToken
     */
    public function saveCode(int $user_id, int $block_id, string $code): HubspotUserToken
    {
        return $this->userTokenRepository->saveCode($user_id, $block_id, $code);
    }

    /**
     * Получение токена по коду (в первый раз)
     *
     * @param int $user_id
     * @param int $block_id
     * @param string $code
     * @return HubspotUserToken
     * @throws GuzzleException
     */
    protected function createNewToken(int $user_id, int $block_id, string $code): HubspotUserToken
    {
        $form_params = [
            'grant_type' => 'authorization_code',
            'client_id' => config('hubspot.client_id'),
            'client_secret' => config('hubspot.secret'),
            'redirect_uri' => $this->getRedirectUrl(),
            'code' => $code,
        ];
        return $this->requestAndSaveToken($user_id, $block_id, $form_params);
    }

    /**
     * Обновление истекшего токена
     *
     * @param int $user_id
     * @param int $block_id
     * @param TokenDTO $tokenDTO
     * @return HubspotUserToken
     * @throws GuzzleException
     */
    protected function refreshToken(int $user_id, int $block_id, TokenDTO $tokenDTO): HubspotUserToken
    {
        $form_params = [
            'grant_type' => 'refresh_token',
            'client_id' => config('hubspot.client_id'),
            'client_secret' => config('hubspot.secret'),
            'redirect_uri' => $this->getRedirectUrl(),
            'refresh_token' => $tokenDTO->refresh_token,
        ];
        return $this->requestAndSaveToken($user_id, $block_id, $form_params);
    }

    /**
     * Получение в Hubspot и сохранение в БД токена
     *
     * @param int $user_id
     * @param array $form_params
     * @param int $block_id
     * @return HubspotUserToken
     * @throws GuzzleException
     */
    protected function requestAndSaveToken(int $user_id, int $block_id, array $form_params): HubspotUserToken
    {
        $tokenDTO = $this->authClient->requestToken($form_params);
        return $this->saveToken($user_id, $block_id, $tokenDTO);
    }

    /**
     * Сохранение токена в БД
     *
     * @param TokenDTO $tokenDTO
     * @param int $user_id
     * @param int $block_id
     * @return HubspotUserToken
     */
    protected function saveToken(int $user_id, int $block_id, TokenDTO $tokenDTO): HubspotUserToken
    {
        return $this->userTokenRepository->saveToken($user_id, $block_id, $tokenDTO);
    }

    /**
     * Ссылка, куда будет направлен пользователь и код, после выдачи прав в Hubspot
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        $url = route('user.integrations.blocks.hubspot.redirect');
        return $this->getCorrectUrl($url);
    }

    /**
     * Hubspot не дает использовать http кроме http://localhost. Поэтому http://secure.usedesk.local/ не работает.
     * Метод для локального теста
     *
     * @param string $url
     * @return string
     */
    protected function getCorrectUrl(string $url): string
    {
        if (config('app.env') === 'local') {
            $url = str_replace(URL::to('/'), 'http://localhost', $url);
        }
        return $url;
    }
}