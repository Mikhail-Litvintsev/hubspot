<?php

namespace App\Services\Integrations\Hubspot;

use App\Repositories\Integrations\HubspotUserSettingsRepository;
use App\VO\Integrations\Hubspot\DealVO;

class HubspotDealSettingsService
{
    public function __construct(
        protected HubspotUserSettingsRepository $userSettingsRepository
    ) {
    }

    /**
     * Обновление настроек отображения полей сделки
     *
     * @param int $user_id
     * @param int $block_id
     * @param array $data
     *
     * @return array
     */
    public function updateDealSettings(int $user_id, int $block_id, array $data): array
    {
        $allSettings = $this->getAllOptionalDealSettings();
        $deal_settings = array_values(array_intersect($data, $allSettings));
        $newHubspotUserSettings = $this->userSettingsRepository->updateDealSettings($user_id, $block_id, $deal_settings);
        return $newHubspotUserSettings->deal_settings;
    }

    /**
     * Получение текущих настроек отображения полей сделки
     *
     * @param int $user_id
     * @param int $block_id
     *
     * @return array
     */
    public function getDealSettings(int $user_id, int $block_id): array
    {
        $hubspotUserSettings = $this->userSettingsRepository->findFirst($user_id, $block_id);
        return $hubspotUserSettings->deal_settings ?? $this->getAllOptionalDealSettings();
    }

    /**
     * Получение всех доступных для настройки полей сделки
     *
     * @return array
     */
    public function getAllOptionalDealSettings(): array
    {
        $dealVO = new DealVO();
        return $dealVO->getOptionalAttributes();
    }
}
