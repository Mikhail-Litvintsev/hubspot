<?php

namespace App\Services\Integrations\Hubspot;

use App\DTO\Integrations\CustomBlocks\ClientDTO;
use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Repositories\Integrations\HubspotClientRepository;
use App\Repositories\Tickets\TicketRepository;
use App\Services\Integrations\Hubspot\HtmlParams\IndexParams;
use App\VO\Integrations\Hubspot\ContactVO;
use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\API\DTO\ContactDTO;

class HubspotContactService extends ApiService
{
    protected TicketRepository $ticketRepository;
    protected HubspotClientRepository $hubspotClientRepository;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        parent::__construct($authService);
        $this->ticketRepository = app(TicketRepository::class);
        $this->hubspotClientRepository = app(HubspotClientRepository::class);
    }

    /**
     * Поиск контакта по id в Hubspot
     *
     * @param int $block_id
     * @param int $user_id
     * @param int $hs_contact_id
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getById(int $user_id, int $block_id, int $hs_contact_id): array
    {
        $contactDTO = $this->createApiClient($user_id, $block_id)->getContactById($hs_contact_id);
        return ContactVO::createFromDTO($contactDTO)->toArray();
    }

    /**
     * Получение предзаполненных данных контакта внутри Юздеск
     *
     * @param int $ticket_id
     * @return array
     */
    public function getContactByTicketId(int $ticket_id): array
    {
        $client = $this->ticketRepository->findClientByTicket($ticket_id);
        $contactDto = new ContactDTO(
            email: ($client->emails[0] ?? null)->email ?? null,
            firstname: $client->name,
            phone: ($client->phones[0] ?? null)->phone ?? null,
            website: ($client->sites[0] ?? null)->url ?? null,
            company: $client->clientCompany->name ?? null
        );
        return ContactVO::createFromDTO($contactDto)->toArray();
    }

    /**
     * Создание контакта в Hubspot
     *
     * @param int $user_id
     * @param int $block_id
     * @param array $contactData
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function createContact(int $user_id, int $block_id, array $contactData): array
    {
        $contactData = array_filter(array_intersect_key($contactData, get_class_vars(ContactDTO::class)));
        $newContact = $this->createApiClient($user_id, $block_id)->createContact(new ContactDTO(...$contactData));
        return ContactVO::createFromDTO($newContact)->toArray();
    }

    /**
     * Поиск контактов по почтам и телефонам в Hubspot
     *
     * @param int $user_id
     * @param int $block_id
     * @param ClientDTO $clientDTO
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function searchContacts(int $user_id, int $block_id, ClientDTO $clientDTO): array
    {
        $phones = array_map(function ($clientPhoneDTO) {
            return $clientPhoneDTO->phone;
        }, $clientDTO->phones);
        $contactsDTO = $this->createApiClient($user_id, $block_id)->searchContacts($clientDTO->emails, $phones);
        $contacts = [];
        foreach ($contactsDTO as $contactDTO) {
            $contacts[] = ContactVO::createFromDTO($contactDTO)->toArray();
        }
        return $contacts;
    }

    /**
     * Поиск id Hubspot контакта, связанного со клиентом и блоком Юздеск
     *
     * @param int $block_id
     * @param int $client_id
     * @return string|null
     */
    public function getLinkedContactId(int $block_id, int $client_id): ?string
    {
        return $this->hubspotClientRepository->findFirst($block_id, $client_id)?->hs_contact_id;
    }

    /**
     * Поиск id Hubspot контакта, связанного со клиентом и блоком Юздеск (поиск по ticket_id)
     *
     * @param int $block_id
     * @param int $ticket_id
     * @return string|null
     */
    public function getLinkedContactIdFromTicketId(int $block_id, int $ticket_id): ?string
    {
        $client_id = $this->ticketRepository->findClientByTicket($ticket_id)?->id;
        return ($client_id) ? $this->getLinkedContactId($block_id, $client_id) : null;
    }
}