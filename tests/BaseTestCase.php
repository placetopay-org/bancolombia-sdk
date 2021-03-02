<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use PlacetoPay\BancolombiaSDK\BancolombiaButton;
use Tests\Cases\ExternalSystemMock;

class BaseTestCase extends TestCase
{
    public function service(array $settings = [], $mockClient = true)
    {
        if ($mockClient) {
            $handler = HandlerStack::create(new ExternalSystemMock());
            $settings['client'] = new Client(['handler' => $handler]);
        }

        return BancolombiaButton::load(
            $settings['identification'] ?? 'mock-client-id',
            $settings['secret'] ?? 'mock-client-secret',
            $settings
        );
    }
}
