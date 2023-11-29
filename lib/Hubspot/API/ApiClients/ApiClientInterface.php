<?php

namespace UseDesk\Hubspot\API\ApiClients;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use UseDesk\Hubspot\API\DTO\ContactDTO;
use UseDesk\Hubspot\API\DTO\DealDTO;
use UseDesk\Hubspot\API\DTO\OwnerDTO;
use UseDesk\Hubspot\API\DTO\Pagination\Meta;
use UseDesk\Hubspot\API\DTO\Pagination\Response;
use UseDesk\Hubspot\API\DTO\PipelineDTO;

interface ApiClientInterface
{
    /**
     * @return ContactDTO[]
     *
     * @throws HubspotApiException
     */
    public function searchContacts(array $emails, array $phones = []): array;

    /**
     * @return ContactDTO[]
     *
     * @throws HubspotApiException
     */
    public function searchContactsByEmail(string $email): array;

    /**
     * @return ContactDTO[]
     *
     * @throws HubspotApiException
     */
    public function searchContactsByPhone(string $phone): array;

    /**
     * @param ContactDTO $contact
     *
     * @return ContactDTO
     *
     * @throws HubspotApiException
     */
    public function createContact(ContactDTO $contact): ContactDTO;

    /**
     * @param int $hs_contact_id
     * @param Meta $meta
     *
     * @return Response
     *
     * @throws HubspotApiException
     */
    public function getDealsFromContactId(int $hs_contact_id, Meta $meta): Response;

    /**
     * @param int $hs_deal_id
     *
     * @return DealDTO
     *
     * @throws HubspotApiException
     */
    public function getDeal(int $hs_deal_id): DealDTO;

    /**
     * @return OwnerDTO []
     *
     * @throws HubspotApiException
     */
    public function getOwners(): array;

    /**
     * @return PipelineDTO []
     *
     * @throws HubspotApiException
     */
    public function getDealPipelines(): array;


    /**
     * @param DealDTO $dealDTO
     * @param int $hs_contact_id
     *
     * @return DealDTO
     *
     * @throws HubspotApiException
     */
    public function createDeal(DealDTO $dealDTO, int $hs_contact_id): DealDTO;

    /**
     * @param int $hs_deal_id
     * @param string $sort
     * @param Meta $meta
     *
     * @return Response
     *
     * @throws HubspotApiException
     */
    public function getNotes(int $hs_deal_id, string $sort, Meta $meta): Response;

    /**
     * @param int $hs_contact_id
     *
     * @return ContactDTO
     *
     * @throws HubspotApiException
     */
    public function getContactById(int $hs_contact_id): ContactDTO;

    /**
     * @param string $token
     */
    public function __construct(string $token);
}
