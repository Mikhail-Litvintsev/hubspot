<?php

namespace UseDesk\Hubspot\Book;

interface ScopeBook
{
    public const SETTINGS_BILLING_WRITE = 'settings.billing.write';
    public const TIMELINE = 'timeline';
    public const TICKETS = 'tickets';
    public const E_COMMERCE = 'e-commerce';
    public const MEDIA_BRIDGE_READ = 'media_bridge.read';
    public const SETTINGS_USERS_TEAMS_WRITE = 'settings.users.teams.write';
    public const SETTINGS_USERS_TEAMS_READ = 'settings.users.teams.read';
    public const SETTINGS_USERS_WRITE = 'settings.users.write';
    public const SETTINGS_USERS_READ = 'settings.users.read';
    public const SETTINGS_CURRENCIES_READ = 'settings.currencies.read';
    public const SETTINGS_CURRENCIES_WRITE = 'settings.currencies.write';
    public const CRM_OBJECTS_OWNERS_READ = 'crm.objects.owners.read';
    public const CRM_OBJECTS_COMPANIES_READ = 'crm.objects.companies.read';
    public const CRM_OBJECTS_CONTACTS_WRITE = 'crm.objects.contacts.write';
    public const CRM_OBJECTS_CONTACTS_READ = 'crm.objects.contacts.read';
    public const CRM_OBJECTS_DEALS_READ = 'crm.objects.deals.read';
    public const CRM_OBJECTS_DEALS_WRITE = 'crm.objects.deals.write';
    public const CRM_OBJECTS_CUSTOM_READ = 'crm.objects.custom.read';
    public const CRM_OBJECTS_CUSTOM_WRITE = 'crm.objects.custom.write';
    public const CRM_OBJECTS_GOALS_READ = 'crm.objects.goals.read';
    public const CRM_OBJECTS_LINE_ITEMS_READ = 'crm.objects.line_items.read';
    public const CRM_OBJECTS_LINE_ITEMS_WRITE = 'crm.objects.line_items.write';
    public const CRM_OBJECTS_QUOTES_READ = 'crm.objects.quotes.read';
    public const CRM_OBJECTS_QUOTES_WRITE = 'crm.objects.quotes.write';
}