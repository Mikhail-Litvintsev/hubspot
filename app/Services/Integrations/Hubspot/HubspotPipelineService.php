<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use GuzzleHttp\Exception\GuzzleException;

class HubspotPipelineService extends ApiService
{
    /**
     * Получение массива deal pipelines с ключами - pipeline_id
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
        $pipelines = $this->createApiClient($user_id, $block_id)->getDealPipelines();
        $result = [];
        foreach ($pipelines as $pipeline) {
            $result[$pipeline->pipelineId] = (array)$pipeline;
        }
        return $result;
    }
}
