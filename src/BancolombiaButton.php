<?php

namespace PlacetoPay\BancolombiaSDK;

use PlacetoPay\BancolombiaSDK\Entities\HealthResponse;
use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\BancolombiaSDK\Entities\TransferIntentRequest;
use PlacetoPay\BancolombiaSDK\Entities\TransferIntentResponse;
use PlacetoPay\BancolombiaSDK\Entities\TransferResponse;
use PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException;
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

    public function request(array $data): TransferIntentResponse
    {
        $response = Carrier::requestCall($this->settings, new TransferIntentRequest($data, $this->settings->hash()));
        return TransferIntentResponse::parseFromResponse($response);
    }

    public function query(string $code): TransferResponse
    {
        $response = Carrier::queryCall($this->settings, $code);
        return TransferResponse::parseFromResponse($response);
    }

    public function health(): HealthResponse
    {
        return new HealthResponse(Carrier::healthCall($this->settings));
    }

    public static function handleCallback(array $posted, $secret): TransferResponse
    {
        $check = [
            $secret,
            $posted['commerceTransferButtonId'],
            $posted['transferCode'],
            $posted['transferAmount'],
            $posted['transferState'],
        ];

        $result = hash('sha512', implode('~', $check));
        if ($result != $posted['sign']) {
            throw BancolombiaException::forBadSignature();
        }

        return TransferResponse::parseFromCallback($posted);
    }

    public static function load(string $identification, string $secret, array $settings = []): self
    {
        return new self(new Settings(array_replace([
            'identification' => $identification,
            'secret' => $secret,
        ], $settings)));
    }
}
