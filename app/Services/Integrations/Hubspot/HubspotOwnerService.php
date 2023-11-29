<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\VO\Integrations\Hubspot\OwnerVO;
use GuzzleHttp\Exception\GuzzleException;

class HubspotOwnerService extends ApiService
{
    /**
     * Получение массива owners с ключами - owner_id
     *
     * @param int $user_id
     * @param int $block_id
     *
     * @return array
     *
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getAllWithIdKey(int $user_id, int $block_id): array
    {
        $owners = $this->createApiClient($user_id, $block_id)->getOwners();
        $result = [];
        foreach ($owners as $owner) {
            $ownerVO = OwnerVO::createFromDTO($owner);
            $result[$owner->id] = $ownerVO->toArray();
        }
        return $result;
    }
}
