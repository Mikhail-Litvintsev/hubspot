<?php

namespace UseDesk\Hubspot\API\Auth;

use UseDesk\Hubspot\API\DTO\TokenDTO;

class TokenService
{
    /**
     * Преобразование TokenDTO в строку
     *
     * @param TokenDTO $tokenDTO
     *
     * @return string
     */
    public function encodeToken(TokenDTO $tokenDTO): string
    {
        return json_encode($tokenDTO);
    }

    /**
     * Получение TokenDTO из строки
     *
     * @param string $hubspotUserToken
     *
     * @return TokenDTO
     */
    public function decodeToken(string $hubspotUserToken): TokenDTO
    {
        $properties = json_decode($hubspotUserToken, true);
        return new TokenDTO(...$properties);
    }
}
