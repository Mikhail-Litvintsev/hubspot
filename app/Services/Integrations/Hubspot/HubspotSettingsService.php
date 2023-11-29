<?php

namespace App\Services\Integrations\Hubspot;

use UseDesk\Hubspot\Book\ScopeBook;

class HubspotSettingsService
{
    /**
     * Список разрешений, которые должен выдать пользователь
     *
     * @return array
     */
    public function getScopes(): array
    {
        return [
            ScopeBook::CRM_OBJECTS_OWNERS_READ,
            ScopeBook::CRM_OBJECTS_COMPANIES_READ,
            ScopeBook::CRM_OBJECTS_CONTACTS_WRITE,
            ScopeBook::CRM_OBJECTS_CONTACTS_READ,
            ScopeBook::CRM_OBJECTS_DEALS_READ,
            ScopeBook::CRM_OBJECTS_DEALS_WRITE,
            ScopeBook::CRM_OBJECTS_CUSTOM_READ,
            ScopeBook::CRM_OBJECTS_CUSTOM_WRITE,
        ];
    }
}
