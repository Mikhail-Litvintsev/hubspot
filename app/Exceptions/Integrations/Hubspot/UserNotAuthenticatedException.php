<?php

namespace App\Exceptions\Integrations\Hubspot;

use Throwable;

class UserNotAuthenticatedException extends \Exception
{
    public function __construct($message = "User is not authenticated at Hubspot", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}