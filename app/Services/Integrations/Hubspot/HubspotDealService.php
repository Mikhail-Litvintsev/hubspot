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

class HubspotDealService extends ApiService
{
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
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getDeal(int $user_id, int $block_id, int $hs_deal_id): array
    {
        $deal = $this->createApiClient($user_id, $block_id)->getDeal($hs_deal_id);
        return $this->getDealVOArrayTakingSettings($user_id, $block_id, $deal);

    }

    /**
     * Поиск всех сделок в Hubspot, связанных с контактом
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $hs_contact_id
     * @return array
     * @throws HubspotApiException
     * @throws UserNotAuthenticatedException|GuzzleException
     */
    public function getDeals(int $user_id, int $block_id, int $hs_contact_id): array
    {
        $deals = $this->createApiClient($user_id, $block_id)->getDealsFromContactId($hs_contact_id);
        $owners = app(HubspotOwnerService::class)->getAllWithIdKey($user_id, $block_id);
        $result = [];
        foreach ($deals as $deal) {
            $additionalProperties = [];
            if (isset($owners[$deal->hubspot_owner_id])) {
                $additionalProperties['owner'] = $owners[$deal->hubspot_owner_id];
            }
            $result[] = $this->getDealVOArrayTakingSettings($user_id, $block_id, $deal, $additionalProperties);
        }
        return $result;
    }

    /**
     * Создание сделки в Hubspot
     *
     * @param int $user_id
     * @param int $block_id
     * @param int $ticket_id
     * @param int $hs_contact_id
     * @param array $dealData
     * @return array
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
        return $this->getDealVOArrayTakingSettings($user_id, $block_id, $newDeal);
    }

    /**
     * Получение DealDTO из инпутов фронта
     *
     * @param array $dealData
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
}