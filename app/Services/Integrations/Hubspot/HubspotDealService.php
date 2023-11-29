<?php

namespace App\Services\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Repositories\Integrations\HubspotClientRepository;
use App\Repositories\Integrations\HubspotUserSettingsRepository;
use App\VO\Integrations\Hubspot\DealVO;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use UseDesk\Hubspot\API\DTO\DealDTO;
use UseDesk\Hubspot\API\DTO\Pagination\Links;
use UseDesk\Hubspot\API\DTO\Pagination\Meta;
use UseDesk\Hubspot\API\DTO\Pagination\Response;

class HubspotDealService extends ApiService
{
    public const PAGINATION_PER_PAGE = 5;
    public const SORT_DEFAULT = 'ASC';
    public const AVAILABLE_SORTS = [
        self::SORT_DEFAULT,
        'DESC'
    ];

    protected HubspotClientRepository $hubspotClientRepository;
    protected HubspotUserSettingsRepository $hubspotUserSettingsRepository;
    public function __construct(AuthService $authService)
    {
        parent::__construct($authService);
        $this->hubspotClientRepository = app(HubspotClientRepository::class);
        $this->hubspotUserSettingsRepository = app(HubspotUserSettingsRepository::class);
    }

    /**
     * Поиск сделки в Hubspot
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_deal_id
     *
     * @return array
     *
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getDeal(int $user_id, int $block_id, int $hs_deal_id): array
    {
        $deal = $this->createApiClient($user_id, $block_id)->getDeal($hs_deal_id);
        $owners = app(HubspotOwnerService::class)->getAllWithIdKey($user_id, $block_id);
        return $this->getDealVoWithOwner($user_id, $block_id, $deal, $owners);

    }

    /**
     * Поиск всех сделок в Hubspot, связанных с контактом
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_contact_id
     * @param Meta $meta
     *
     * @return Response
     *
     * @throws GuzzleException
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException
     */
    public function getDeals(int $user_id, int $block_id, int $hs_contact_id, Meta $meta): Response
    {
        $deals = $this->createApiClient($user_id, $block_id)->getDealsFromContactId($hs_contact_id, $meta);
        $owners = app(HubspotOwnerService::class)->getAllWithIdKey($user_id, $block_id);
        $result = [];
        foreach ($deals->data as $deal) {
            $result[] = $this->getDealVoWithOwner($user_id, $block_id, $deal, $owners);
        }
        return new Response(data: $result, meta: $deals->meta, links: new Links());
    }

    /**
     * Получение отфильтрованной сделки с добавлением владельца
     *
     * @param int $user_id
     * @param int $block_id
     * @param DealDTO $deal
     * @param array $owners
     *
     * @return array
     */
    protected function getDealVoWithOwner(int $user_id, int $block_id, DealDTO $deal, array $owners): array
    {
        $additionalProperties = [];
        if (isset($owners[$deal->hubspot_owner_id])) {
            $additionalProperties['owner'] = $owners[$deal->hubspot_owner_id];
        }
        return $this->getDealVOArrayTakingSettings($user_id, $block_id, $deal, $additionalProperties);
    }

    /**
     * Создание сделки в Hubspot
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     * @param array $dealData
     *
     * @return array
     *
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function createDeal(
        int $user_id,
        int $block_id,
        int $ticket_id,
        int $hs_contact_id,
        array $dealData
    ): array {
        $newDeal = $this->createApiClient($user_id, $block_id)
            ->createDeal($this->getDealDTO($dealData), $hs_contact_id);

        $this->hubspotClientRepository->update($block_id, $ticket_id, $hs_contact_id);
        $this->waitForDealConfirm($user_id, $block_id, $newDeal->hs_object_id);
        $owners = app(HubspotOwnerService::class)->getAllWithIdKey($user_id, $block_id);
        return $this->getDealVoWithOwner($user_id, $block_id, $newDeal, $owners);
    }

    /**
     * Получение DealDTO из инпутов фронта
     *
     * @param array $dealData
     *
     * @return DealDTO
     */
    public function getDealDTO(array $dealData): DealDTO
    {
        $params = array_filter($dealData);

        if (isset($params['closedate'])) {
            $closedate = Carbon::createFromFormat('Y-m-d\TH:i', $params['closedate']);
            $params['closedate'] = $closedate->timezone('UTC')->format('Y-m-d\TH:i:s.v\Z');
        }
        $params = array_intersect_key($params, get_class_vars(DealDTO::class));
        return new DealDTO(...$params);
    }


    /**
     * Получение сделки в виде отфильтрованного (по настройкам пользователя) массива
     *
     * @param int $user_id
     * @param int $block_id
     * @param DealDTO $deal
     * @param array $additionalProperties
     *
     * @return array
     */
    protected function getDealVOArrayTakingSettings(
        int $user_id,
        int $block_id,
        DealDTO $deal,
        array $additionalProperties = []
    ): array {
        $dealVO = DealVO::createFromDTO($deal, $additionalProperties);
        $settings = $this->hubspotUserSettingsRepository->scopeDealSettings($user_id, $block_id);
        return ($settings === null) ? $dealVO->toArray() : $dealVO->filterToArray($settings) ;
    }

    /**
     * Ожидание, пока в Hubspot появятся данные о новой сделке
     *
     * @param int $user_id
     * @param int $block_id
     * @param int|null $hs_object_id
     *
     * @return void
     */
    protected function waitForDealConfirm(int $user_id, int $block_id, ?int $hs_object_id): void
    {
        for ($i = 0; $i < 10; $i++) {
            try {
                $this->getDeal($user_id, $block_id, $hs_object_id);
                break;
            } catch (GuzzleException|UserNotAuthenticatedException|HubspotApiException $exception) {
                sleep(1);
            }
        }
    }

    /**
     * Создает Meta из массива с фронта и настроек в БД
     *
     * @param int $user_id
     * @param int $block_id
     * @param array|null $meta
     *
     * @return Meta
     */
    public function getDealPaginationMeta(int $user_id, int $block_id, ?array $meta): Meta
    {
        $settings = $this->hubspotUserSettingsRepository->scopeDealSettings($user_id, $block_id);
        $meta = (is_array($meta)) ? $meta : [];
        $meta['per_page'] = $settings['pagination']['per_page'] ?? self::PAGINATION_PER_PAGE;
        return new Meta(...$meta);
    }

    /**
     * Преобразует метод сортировки полученный с фронта в доступный для сервиса
     *
     * @param string|null $sort
     *
     * @return string
     */
    public function getSort(?string $sort): string
    {
        $sort = strtoupper($sort ?? '');
        return (in_array($sort, self::AVAILABLE_SORTS)) ? $sort : self::SORT_DEFAULT;
    }
}
