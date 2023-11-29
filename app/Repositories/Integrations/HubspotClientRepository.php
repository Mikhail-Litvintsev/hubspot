<?php

declare(strict_types=1);

namespace App\Repositories\Integrations;

use App\Models\Integrations\Hubspot\HubspotClient;
use App\Repositories\Tickets\TicketRepository;

class HubspotClientRepository
{
    /**
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     *
     * @return HubspotClient
     */
    public function update(int $block_id, int $ticket_id, int $hs_contact_id): HubspotClient
    {
        $client = app(TicketRepository::class)->findClientByTicket($ticket_id);
        $hubspotClient = HubspotClient::where(['block_id' => $block_id, 'client_id' => $client->id]);
        if ($hubspotClient->onlyTrashed()->first()?->id) {
            $hubspotClient->onlyTrashed()->first()->restore();
        }
        return HubspotClient::updateOrCreate(
            ['block_id' => $block_id, 'client_id' => $client->id],
            ['hs_contact_id' => $hs_contact_id]
        );
    }

    /**
     * @param int $block_id
     * @param int $client_id
     *
     * @return HubspotClient|null
     */
    public function findFirst(int $block_id, int $client_id): ?HubspotClient
    {
        return HubspotClient::where(['block_id' => $block_id, 'client_id' => $client_id])->first();
    }
}
