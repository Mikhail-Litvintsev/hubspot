<?php

namespace App\DTO\Integrations\CustomBlocks;

use Exception;
use UseDesk\Hubspot\API\DTO\DTOFactory;

class ClientDTO
{
    public readonly ?string $name;
    public readonly ?string $company;
    public readonly array $emails;
    /** @var ClientPhoneDTO[]  */
    public readonly array $phones;
    /** @var SocialServiceDTO[] */
    public readonly array $social_services;
    /** @var AddressDTO[] */
    public readonly array $addresses;
    /** @var MessengerDTO[] */
    public readonly array $messengers;
    public readonly array $sites;

    /**
     * @throws Exception
     */
    public function __construct(
        ?string $name = null,
        ?string $company = null,
        array $emails = [],
        array $phones = [],
        array $social_services = [],
        array $addresses = [],
        array $messengers = [],
        array $sites = [],
    ) {
        $this->name = $name;
        $this->company = $company;
        $this->emails = $emails;
        $this->sites = $sites;
        $service = app(DTOFactory::class);
        $this->phones = $service->createList($phones, ClientPhoneDTO::class);
        $this->social_services = $service->createList($social_services, SocialServiceDTO::class);
        $this->messengers = $service->createList($messengers, MessengerDTO::class);
        $this->addresses = $service->createList($addresses, AddressDTO::class);
    }
}