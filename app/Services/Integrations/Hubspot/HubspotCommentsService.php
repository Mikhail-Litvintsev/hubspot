<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\VO\Integrations\Hubspot\CommentVO;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Pagination\LengthAwarePaginator;
use UseDesk\Hubspot\API\DTO\Pagination\Meta;

class HubspotCommentsService extends ApiService
{
    /**
     * Получение комментариев, связанных со сделкой для фронта.
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_deal_id
     * @param string $sort
     * @param Meta $meta
     *
     * @return LengthAwarePaginator
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getCommentsByHsDealId(int $user_id, int $block_id, int $hs_deal_id, string $sort, Meta $meta): LengthAwarePaginator
    {
        $notes = [];
        $response = $this->createApiClient($user_id, $block_id)->getNotes(hs_deal_id: $hs_deal_id, sort: $sort, meta: $meta);
        foreach ($response->data as $note) {
            $notes[] = CommentVO::createFromDTO($note)->toArray();
        }
        return new LengthAwarePaginator($notes, $response->meta->total, $response->meta->per_page, $response->meta->current_page);
    }
}
