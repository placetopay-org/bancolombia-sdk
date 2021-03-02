<?php

namespace PlacetoPay\BancolombiaSDK;

use PlacetoPay\BancolombiaSDK\Entities\HealthResponse;
use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\BancolombiaSDK\Entities\TransferIntentRequest;
use PlacetoPay\BancolombiaSDK\Entities\TransferIntentResponse;
use PlacetoPay\BancolombiaSDK\Helpers\Carrier;

class BancolombiaButton
{
    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }


    public function health(): HealthResponse
    {
        return new HealthResponse(Carrier::healthCall($this->settings));
    }


    public static function load(string $identification, string $secret, array $settings = []): self
    {
        return new self(new Settings(array_replace([
            'identification' => $identification,
            'secret' => $secret,
        ], $settings)));
    }
}
