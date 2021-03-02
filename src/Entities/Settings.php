<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

use GuzzleHttp\Client;
use PlacetoPay\BancolombiaSDK\Helpers\Cache;
use PlacetoPay\BancolombiaSDK\Helpers\Logger;

class Settings
{
    protected $identification;
    protected $secret;
    protected $hash;

    protected $authUrl;
    protected $serviceUrl;

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(array $settings)
    {
        $this->identification = $settings['identification'] ?? '';
        $this->secret = $settings['secret'] ?? '';
        $this->hash = $settings['hash'] ?? '';

        $this->authUrl = $settings['authUrl'] ?? 'https://api.us.apiconnect.ibmcloud.com/bancolombiabluemix-dev/sandbox/v1/security/oauth-otp-pymes/oauth2/token';
        $this->serviceUrl = $settings['serviceUrl'] ?? 'https://sbapi.bancolombia.com/v2/operations/cross-product/payments/payment-order/';

        $this->logger = new Logger($settings['logger'] ?? null);
        $this->cache = new Cache($settings['cache'] ?? null);
        $this->client = $settings['client'] ?? new Client();
    }

    public function identification(): string
    {
        return $this->identification;
    }

    public function secret(): string
    {
        return $this->secret;
    }

    public function authUrl(): string
    {
        return $this->authUrl;
    }

    public function hash()
    {
        return $this->hash;
    }

    public function serviceUrl(string $path = '')
    {
        return $this->serviceUrl . $path;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function cache(): Cache
    {
        return $this->cache;
    }
}
