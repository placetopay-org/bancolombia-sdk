<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

use GuzzleHttp\Client;
use PlacetoPay\BancolombiaSDK\Helpers\Cache;
use PlacetoPay\BancolombiaSDK\Helpers\Logger;
use PlacetoPay\Tangram\Entities\BaseSettings;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Settings extends BaseSettings
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var Logger
     */
    protected Logger $logger;
    /**
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @throws \PlacetoPay\Tangram\Exceptions\InvalidSettingException
     */
    public function __construct(array $settings, OptionsResolver $optionsResolver = null)
    {
        $settings['identification'] = $settings['identification'] ?? '';
        $settings['secret'] = $settings['secret'] ?? '';
        $settings['hash'] = $settings['hash'] ?? '';
        $settings['authUrl'] = $settings['authUrl'] ?? 'https://api.us.apiconnect.ibmcloud.com/bancolombiabluemix-dev/sandbox/v1/security/oauth-otp-pymes/oauth2/token';
        $settings['serviceUrl'] = $settings['serviceUrl'] ?? 'https://sbapi.bancolombia.com/v2/operations/cross-product/payments/payment-order/';

        $this->logger = new Logger($settings['logger'] ?? null);
        $this->cache = new Cache($settings['cache'] ?? null);
        $this->client = $settings['client'] ?? new Client();

        parent::__construct($settings, $optionsResolver);
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
