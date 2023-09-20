<?php

namespace App\DTO\Integrations\CustomBlocks;

class RequestDTO
{
    public readonly ClientDTO $client_data;
    public readonly ChannelDTO $channel_data;

    public function __construct(
        public readonly int $ticket_id,
        public readonly ?string $subject = null,
        public readonly ?int $client_id = null,
        public readonly ?string $channel_type = null,
        public readonly ?int $channel_id = null,
        public readonly ?string $contact = null,
        public readonly ?string $from_email = null,
        array $client_data = [],
        array $channel_data = [],
        public readonly ?int $is_auto_load = null,
        public readonly ?string $q = null,
        public readonly ?int $timeout = null,
    ) {
        $this->client_data = new ClientDTO(...$client_data);
        $this->channel_data = new ChannelDTO(...$channel_data);
    }
}