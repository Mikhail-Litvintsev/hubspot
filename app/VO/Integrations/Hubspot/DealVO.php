<?php

namespace App\VO\Integrations\Hubspot;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class DealVO extends HubspotEntityVO
{
    public readonly ?string $amount;
    public readonly ?string $hs_object_id;
    public readonly ?string $closedate;
    public readonly ?string $createdate;
    public readonly ?string $dealname;
    public readonly ?string $dealstage;
    public readonly ?string $hs_lastmodifieddate;
    public readonly ?string $pipeline;
    public readonly ?string $dealtype;
    public readonly ?string $hs_priority;
    public readonly ?array $owner;
    public readonly ?string $hubspot_owner_id;

    public const REQUIRED_ATTRIBUTES = [
        'hs_object_id',
        'hubspot_owner_id',
        'dealname',
        'pipeline',
        'dealstage',
    ];
    public function __construct(
        ?string $amount = null,
        ?string $hs_object_id = null,
        ?string $closedate = null,
        ?string $createdate = null,
        ?string $dealname = null,
        ?string $dealstage = null,
        ?string $hs_lastmodifieddate = null,
        ?string $pipeline = null,
        ?string $dealtype = null,
        ?string $hs_priority = null,
        ?string $hubspot_owner_id = null,
        ?array $owner = null,
    ) {
        $this->amount = $amount;
        $this->hs_object_id = $hs_object_id;
        $this->closedate = $this->getDate($closedate);
        $this->createdate = $this->getDate($createdate);
        $this->dealname = $dealname;
        $this->dealstage = $dealstage;
        $this->hs_lastmodifieddate = $this->getDate($hs_lastmodifieddate);
        $this->pipeline = $pipeline;
        $this->dealtype = $dealtype;
        $this->hs_priority = $hs_priority;
        $this->hubspot_owner_id = $hubspot_owner_id;
        $this->owner = $owner;
    }

    /**
     * Возвращает массив отфильтрованных по условиям значений, учитывая обязательные поля
     *
     * @param array $properties
     *
     * @return array
     */
    public function filterToArray(array $properties = []): array
    {
        $deal = $this->toArray();
        $properties = array_unique(array_merge(self::REQUIRED_ATTRIBUTES, $properties));
        return array_intersect_key($deal, array_flip($properties));
    }

    /**
     * Возвращает все НЕ обязательные значения
     *
     * @return array
     */
    public function getOptionalAttributes(): array
    {
        $deal = $this->toArray();
        $attributes = array_diff(array_keys($deal), self::REQUIRED_ATTRIBUTES);
        return array_values($attributes);
    }
    /**
     * Преобразует Hubspot дату в стандарт HTML
     *
     * @param string|null $date
     *
     * @return string|null
     */
    private function getDate(?string $date): ?string
    {
        try {
            return Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $date)->timezone('UTC')->format('Y-m-d\TH:i');
        } catch (InvalidFormatException $exception) {
            return $date;
        }
    }
}
