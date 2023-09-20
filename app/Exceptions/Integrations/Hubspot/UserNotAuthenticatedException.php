<?php

namespace App\Exceptions\Integrations\Hubspot;

use App\Exceptions\BaseException;
use Throwable;

class UserNotAuthenticatedException extends BaseException
{
    public function __construct($message = "User is not authenticated at Hubspot", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}