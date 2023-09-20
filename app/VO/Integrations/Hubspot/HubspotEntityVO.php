<?php

namespace App\VO\Integrations\Hubspot;

class HubspotEntityVO
{
    public static function createFromDTO(object $dto, array $additionalProperties = []): static
    {
        $properties = array_intersect_key(get_object_vars($dto), get_class_vars(static::class));
        $properties = array_merge($properties, $additionalProperties);
        return new static(...$properties);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}