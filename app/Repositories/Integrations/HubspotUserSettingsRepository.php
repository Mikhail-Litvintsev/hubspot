<?php

declare(strict_types=1);

namespace App\Repositories\Integrations;

use App\Models\Integrations\Hubspot\HubspotClient;
use App\Models\Integrations\Hubspot\HubspotUserSettings;
use App\Repositories\Tickets\TicketRepository;

class HubspotUserSettingsRepository
{
    /**
     * @param int $user_id
     * @param int $block_id
     * @return HubspotUserSettings|null
     */
    public function findFirst(int $user_id, int $block_id): ?HubspotUserSettings
    {
        return HubspotUserSettings::where(['user_id' => $user_id, 'block_id' => $block_id])->first();
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @return array|null
     */
    public function scopeDealSettings(int $user_id, int $block_id): array|null
    {
        return $this->findFirst($user_id, $block_id)?->deal_settings;
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param array $deal_settings
     * @return HubspotUserSettings
     */
    public function updateDealSettings(int $user_id, int $block_id, array $deal_settings): HubspotUserSettings
    {
        $hubspotUserSettings = HubspotUserSettings::where(['user_id' => $user_id, 'block_id' => $block_id]);
        if ($hubspotUserSettings?->onlyTrashed()?->first()?->id) {
            $hubspotUserSettings?->onlyTrashed()->first()->restore();
        }
        return HubspotUserSettings::updateOrCreate(['user_id' => $user_id, 'block_id' => $block_id],
            ['deal_settings' => $deal_settings]);
    }
}