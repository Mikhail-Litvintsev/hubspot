<?php

namespace App\Exceptions\Integrations\Hubspot;

use Throwable;

class HubspotApiException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $fullMessage = 'Hubspot response error';
        $fullMessage .= (empty($message)) ? '' : " {$message}";
        parent::__construct($fullMessage, $code, $previous);
    }
}