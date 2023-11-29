<?php

namespace App\Services\Integrations\Hubspot\HtmlParams;

use App\CompanyCustomBlock;
use App\DTO\Integrations\CustomBlocks\RequestDTO;
use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Repositories\Integrations\HubspotClientRepository;
use App\Services\CustomBlocks\DynamicBlockRequestData;
use App\Services\Integrations\Hubspot\HubspotContactService;
use App\Services\Integrations\Hubspot\HubspotDealService;
use App\Services\Integrations\Hubspot\HubspotPipelineService;
use GuzzleHttp\Exception\GuzzleException;

class IndexParams
{
    public function __construct(
        protected readonly HubspotContactService $contactService,
        protected readonly HubspotDealService $dealService,
        protected readonly HubspotClientRepository $hubspotClientRepository,
        protected readonly HubspotPipelineService $pipelineService,
    ) {
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param RequestDTO $requestDTO
     * @param array|null $meta
     *
     * @return int[]
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexByDTO(int $user_id, int $block_id, RequestDTO $requestDTO, ?array $meta = null): array
    {
        $params = ['block_id' => $block_id];
        $is_linked = false;
        $contact = [];
        $hs_contact_id = $this->contactService->getLinkedContactId($block_id, $requestDTO->client_id);
        if ($hs_contact_id) {
            try {
                $contact = $this->contactService->getById($user_id, $block_id, $hs_contact_id);
                $is_linked = true;
            } catch (HubspotApiException $exception) {
                /**
                 * Ошибка в случае, если контакт в системе Hubspot не найден.
                 * Такое может случиться, если пользователь вручную на сайте Hubspot удалит контакт.
                 */
            }
        }
        $params['contacts'] = $this->contactService->searchContacts($user_id, $block_id, $requestDTO->client_data);
        $params['is_linked'] = $is_linked;
        $params['hs_contact_id'] = $hs_contact_id;
        $meta = $this->dealService->getDealPaginationMeta($user_id, $block_id, $meta);
        $params['deals'] = ($is_linked) ? $this->dealService->getDeals($user_id, $block_id, $hs_contact_id, $meta) : [];
        $params['contact'] = $contact;
        $params['ticket_id'] = $requestDTO->ticket_id;
        $params['pipelines'] = $this->pipelineService->getAllWithIdKey($user_id, $block_id);

        return $params;
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param array $request
     *
     * @return int[]
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexByRequest(int $user_id, int $block_id, array $request): array
    {
        $dynamicBlockRequestData = app(DynamicBlockRequestData::class);
        $block = CompanyCustomBlock::find($block_id);
        $requestDTO = $dynamicBlockRequestData->getDTO($request, $block);
        $meta = $request['meta'] ?? null;
        return $this->getForIndexByDTO($user_id, $block_id, $requestDTO, $meta);
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param array $request
     *
     * @return array|int[]
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexWithNewContact(int $user_id, int $block_id, array $request): array
    {
        $params = $this->getForIndexByRequest($user_id, $block_id, $request);
        $contacts = $params['contacts'] ?? [];
        $contacts[] = $this->contactService->createContact($user_id, $block_id, $request);
        $params['contacts'] = $contacts;
        return $params;
    }

    /**
     * @param int $user_id
     * @param array $request
     *
     * @return int[]
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForIndexAfterLinkContact(int $user_id, array $request): array
    {
        $block_id = $request['block_id'];
        $ticket_id = $request['ticket_id'];
        $hs_contact_id = $request['hs_contact_id'];
        $this->hubspotClientRepository->update($block_id, $ticket_id, $hs_contact_id);
        return $this->getForIndexByRequest($user_id, $block_id, $request);
    }
}
