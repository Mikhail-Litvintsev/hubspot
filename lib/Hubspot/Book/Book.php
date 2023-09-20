<?php

namespace UseDesk\Hubspot\Book;

interface Book
{
    public const NAME = 'hubspot';
    public const CLIENT_ID = 'client_id';
    public const OAUTH_URL = 'https://app.hubspot.com/oauth/authorize';
    public const SCOPE = 'scope';
    public const CONTACT = 'contact';
    public const CODE = 'code';
    public const STATE = 'state';
    public const SCOPE_SEPARATOR = '%20';
    public const REDIRECT_URI = 'redirect_uri';
}