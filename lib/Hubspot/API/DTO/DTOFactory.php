<?php

namespace UseDesk\Hubspot\API\DTO;

use Exception;

class DTOFactory
{
    /**
     * @throws Exception
     */
    public function create(string $dtoClass, array $properties)
    {
        if (class_exists($dtoClass)) {
            $properties = array_intersect_key($properties, get_class_vars($dtoClass));
            return new $dtoClass(...$properties);
        }
        throw new Exception("Class  $dtoClass does not exist");
    }

    /**
     * @throws Exception
     */
    public function createList(array $dtosPropertylist, string $dtoClass): array
    {
        $list = [];
        foreach ($dtosPropertylist as $item) {
            $list[] = $this->create($dtoClass, $item);
        }
        return $list;
    }
}
