<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

class HealthResponse
{
    protected $isHealthy;

    public function __construct(bool $healthy)
    {
        $this->isHealthy = $healthy;
    }

    public function isHealthy(): bool
    {
        return $this->isHealthy ?? false;
    }
}
