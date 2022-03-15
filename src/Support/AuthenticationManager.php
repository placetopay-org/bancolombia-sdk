<?php

namespace PlacetoPay\BancolombiaSDK\Support;

use GuzzleHttp\Exception\BadResponseException;
use PlacetoPay\BancolombiaSDK\Exceptions\AuthenticationException;
use PlacetoPay\BancolombiaSDK\Parsers\AuthenticationParser;
use PlacetoPay\Base\Constants\Operations;
use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Carriers\RestCarrier;
use PlacetoPay\Tangram\Entities\BaseSettings;
use PlacetoPay\Tangram\Entities\CarrierDataObject;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class AuthenticationManager
{
    private RestCarrier $carrier;
    private AuthenticationParser $authenticationParser;
    private BaseSettings $settings;
    private CacheInterface $cache;

    public function __construct(RestCarrier $carrier, BaseSettings $settings)
    {
        $this->carrier = $carrier;
        $this->settings = $settings;
        $this->authenticationParser = new AuthenticationParser($settings);
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthenticationException
     */
    public function getToken(): string
    {
        $token = $this->cache->get($this->settings->get('identification'));
        if (!$token) {
            $token = $this->authenticate();
        }

        if (!$token) {
            throw new AuthenticationException('Empty authentication response.');
        }

        return $token;
    }

    protected function authenticate(): ?string
    {
        $carrierDataObject = new CarrierDataObject(
            Operations::AUTHENTICATION,
            null,
            [
                'method' => 'POST',
                'endpoint' => $this->settings->get('authUrl'),
            ]
        );

        $carrierDataObject->setRequest($this->authenticationParser->parserRequest($carrierDataObject));

        try {
            $this->carrier->request($carrierDataObject)->response();

            $token = $this->parseResponse($carrierDataObject);
        } catch (BadResponseException $e) {
            throw AuthenticationException::forFailedAuthenticationResponse($e);
        }

        return $token;
    }

    /**
     * @throws AuthenticationException
     */
    protected function parseResponse(CarrierDataObject $carrierDataObject): Transaction
    {
        $this->authenticationParser->errorHandler($carrierDataObject);
        return $this->authenticationParser->parserResponse($carrierDataObject);
    }
}
