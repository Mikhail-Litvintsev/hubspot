<?php

namespace App\Services\Integrations\Hubspot\HtmlParams;

use App\CompanyCustomBlock;
use App\DTO\Integrations\CustomBlocks\RequestDTO;
use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Services\CustomBlocks\DynamicBlockRequestData;
use App\Services\Integrations\Hubspot\HubspotContactService;
use App\Services\Integrations\Hubspot\HubspotDealService;
use GuzzleHttp\Exception\GuzzleException;

class IndexParams
{
    public function __construct(
        protected readonly HubspotContactService $contactService,
        protected readonly HubspotDealService $dealService
    ) {
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param RequestDTO $requestDTO
     * @return int[]
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexByDTO(int $user_id, int $block_id, RequestDTO $requestDTO): array
    {
        $params = ['block_id' => $block_id];
        $is_linked = false;
        $contact = [];
        $hs_contact_id = $this->contactService->getLinkedContactId($block_id, $requestDTO->client_id);
        if ($hs_contact_id)
            try {
            $contact = $this->contactService->getById($user_id, $block_id, $hs_contact_id);
            $is_linked = true;
        } catch (HubspotApiException $exception) {
            /**
             * Ошибка в случае, если контакт в системе Hubspot не найден.
             * Такое может случиться, если пользователь вручную на сайте Hubspot удалит контакт.
             */
            }
        $params['contacts'] = $this->contactService->searchContacts($user_id, $block_id, $requestDTO->client_data);
        $params['is_linked'] = $is_linked;
        $params['hs_contact_id'] = $hs_contact_id;
        $params['deals'] = ($is_linked) ? $this->dealService->getDeals($user_id, $block_id, $hs_contact_id) : [];
        $params['contact'] = $contact;
        $params['ticket_id'] = $requestDTO->ticket_id;
        return $params;
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param array $request
     * @return int[]
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexByRequest(int $user_id, int $block_id, array $request): array
    {
        $dynamicBlockRequestData = app(DynamicBlockRequestData::class);
        $block = CompanyCustomBlock::find($block_id);
        $requestDTO = $dynamicBlockRequestData->getDTO($request, $block);
        return $this->getForIndexByDTO($user_id, $block_id, $requestDTO);
    }

    public function getForIndexWithNewContact(int $user_id, int $block_id, array $request)
    {
        $params = $this->getForIndexByRequest($user_id, $block_id, $request);
        $params['contacts'][] = $this->contactService->createContact($user_id, $block_id,$request);
        return $params;
    }
}