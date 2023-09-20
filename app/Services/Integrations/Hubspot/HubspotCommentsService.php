<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\VO\Integrations\Hubspot\CommentVO;
use GuzzleHttp\Exception\GuzzleException;

class HubspotCommentsService extends ApiService
{
    /**
     * Получение комментариев, связанных со сделкой для фронта.
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_deal_id
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getCommentsByHsDealId(int $user_id, int $block_id, int $hs_deal_id): array
    {
        $notes = [];
        foreach ($this->createApiClient($user_id, $block_id)->getNotes($hs_deal_id) as $note) {
            $notes[] = CommentVO::createFromDTO($note)->toArray();
        }
        return $notes;
    }
}