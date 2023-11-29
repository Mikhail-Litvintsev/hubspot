<?php

namespace App\Services\Integrations\Hubspot\HtmlParams;

use App\Enums\Translators\ArrayAssociativeKeyEnum;
use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Helpers\TranslatorEnumToArray;
use App\Services\Integrations\Hubspot\HubspotCommentsService;
use App\Services\Integrations\Hubspot\HubspotContactService;
use App\Services\Integrations\Hubspot\HubspotDealService;
use App\Services\Integrations\Hubspot\HubspotOwnerService;
use App\Services\Integrations\Hubspot\HubspotPipelineService;
use App\VO\Integrations\Hubspot\DealVO;
use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\Enum\DealPriorityEnum;
use UseDesk\Hubspot\Enum\DealTypeEnum;

class ContactDealsParams
{
    public function __construct(
        protected readonly HubspotContactService $contactService,
        protected readonly HubspotDealService $dealService,
        protected readonly HubspotOwnerService $ownerService,
        protected readonly HubspotPipelineService $pipelineService,
        protected readonly HubspotCommentsService $commentsService,
    ) {
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     * @param array|null $meta
     *
     * @return array
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForContactDeals(int $user_id, int $block_id, int $ticket_id, int $hs_contact_id, ?array $meta = null): array
    {
        $meta = $this->dealService->getDealPaginationMeta($user_id, $block_id, $meta);
        $deals = $this->dealService->getDeals($user_id, $block_id, $hs_contact_id, $meta);
        $is_linked = (bool)$this->contactService->getLinkedContactIdFromTicketId($block_id, $ticket_id);
        $contact = $this->contactService->getById($user_id, $block_id, $hs_contact_id);
        $pipelines = $this->pipelineService->getAllWithIdKey($user_id, $block_id);

        return compact('hs_contact_id', 'deals', 'is_linked', 'contact', 'pipelines');
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_contact_id
     *
     * @return array
     *
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     * @throws GuzzleException
     */
    public function getForContactDealCreate(int $user_id, int $block_id, int $hs_contact_id): array
    {
        $deal = (array)(new DealVO());
        $owners = $this->ownerService->getAllWithIdKey($user_id, $block_id);
        $pipelines = $this->pipelineService->getAllWithIdKey($user_id, $block_id);
        $priorities = app(TranslatorEnumToArray::class)->translate(
            DealPriorityEnum::cases(),
            ArrayAssociativeKeyEnum::NAMED_KEYS
        );
        $deal_types = app(TranslatorEnumToArray::class)->translate(
            DealTypeEnum::cases(),
            ArrayAssociativeKeyEnum::NAMED_KEYS
        );
        return compact('hs_contact_id', 'deal', 'owners', 'pipelines', 'priorities', 'deal_types');
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     * @param array $request
     *
     * @return array
     *
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getForContactDealStore(int $user_id, int $block_id, int $ticket_id, int $hs_contact_id, array $request): array
    {
        $deal = $this->dealService->createDeal(
            $user_id,
            $block_id,
            $ticket_id,
            $hs_contact_id,
            $request
        );
        $meta = $this->dealService->getDealPaginationMeta($user_id, $block_id, null);
        $sort = $this->dealService->getSort(null);
        $comments = $this->commentsService->getCommentsByHsDealId(user_id: $user_id, block_id: $block_id, hs_deal_id: $deal['hs_object_id'], sort: $sort, meta: $meta);
        $owners = $this->ownerService->getAllWithIdKey($user_id, $block_id);
        $pipelines = $this->pipelineService->getAllWithIdKey($user_id, $block_id);

        return  compact('deal', 'comments', 'owners', 'hs_contact_id', 'pipelines', 'sort');
    }

    /**
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_deal_id
     * @param int $hs_contact_id
     * @param array|null $meta
     * @param string|null $sort
     *
     * @return array
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getForContactDealShow(int $user_id, int $block_id, int $hs_deal_id, int $hs_contact_id, ?string $sort, ?array $meta, ?int $last_page_number,): array
    {
        $deal = $this->dealService->getDeal($user_id, $block_id, $hs_deal_id);
        $sort = $this->dealService->getSort($sort);
        $meta = $this->dealService->getDealPaginationMeta($user_id, $block_id, $meta);
        $comments = $this->commentsService->getCommentsByHsDealId(user_id: $user_id, block_id: $block_id, hs_deal_id: $hs_deal_id, sort: $sort, meta: $meta);
        $owners = $this->ownerService->getAllWithIdKey($user_id, $block_id);
        $pipelines = $this->pipelineService->getAllWithIdKey($user_id, $block_id);

        return compact('deal', 'comments', 'owners', 'hs_contact_id', 'pipelines', 'sort', 'last_page_number');
    }
}
