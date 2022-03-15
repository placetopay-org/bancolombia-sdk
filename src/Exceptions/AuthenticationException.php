<?php

namespace PlacetoPay\BancolombiaSDK\Exceptions;

use Exception;

class AuthenticationException extends BancolombiaException
{
    public static function forFailedAuthenticationResponse(Exception $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
