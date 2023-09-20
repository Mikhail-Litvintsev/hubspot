<?php

namespace App\Services\Integrations\Hubspot\HtmlParams;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Services\Integrations\Hubspot\HubspotDealSettingsService;
use GuzzleHttp\Exception\GuzzleException;

class DealSettingsParams
{
    public function __construct(
        protected HubspotDealSettingsService $dealSettingsService,
        protected ContactDealsParams $dealsParams
    ) {
    }

    /**
     * Параметры для view первичного редактирования настроек полей сделки
     *
     * @param int $user_id
     * @param int $block_id
     * @return array
     */
    public function getForEdit(int $user_id, int $block_id, int $hs_contact_id): array
    {
        $all_settings = $this->dealSettingsService->getAllOptionalDealSettings();
        $deal_settings = $this->dealSettingsService->getDealSettings($user_id, $block_id);
        return compact('all_settings', 'deal_settings', 'hs_contact_id');
    }

    /**
     * Обновление настроек и возврат параметров для view списка сделок
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     * @param array $data
     * @return array
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForUpdate(int $user_id, int $block_id, int $ticket_id, int $hs_contact_id, array $data): array
    {
        $this->dealSettingsService->updateDealSettings($user_id, $block_id, $data);
        return $this->dealsParams->getForContactDeals($user_id, $block_id, $ticket_id, $hs_contact_id);
    }
}