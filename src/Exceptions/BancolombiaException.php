<?php

namespace PlacetoPay\BancolombiaSDK\Exceptions;

use Exception;

class BancolombiaException extends Exception
{
    public static function forBadSignature()
    {
        return new self('Bad signature provided, this is not a verifiable response');
    }

    public static function forInvalidRequest(string $parameter): self
    {
        return new self('Bad request provided, the parameter ' . $parameter . ' does not validate');
    }
}
