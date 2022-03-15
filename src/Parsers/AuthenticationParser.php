<?php

namespace PlacetoPay\BancolombiaSDK\Parsers;

use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\BancolombiaSDK\Entities\Token;
use PlacetoPay\BancolombiaSDK\Exceptions\AuthenticationException;
use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;
use PlacetoPay\Tangram\Entities\BaseSettings;
use Psr\Http\Message\ResponseInterface;

class AuthenticationParser implements ParserHandlerContract
{
    private BaseSettings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        return [
           'form_params' => [
               'grant_type' => 'client_credentials',
               'scope' => 'Transfer-Intention:write:app Transfer-Intention:read:app',
               'redirect_uri' => 'https://dnetix.co/ping/auth_call',
               'client_id' => $this->settings->get('identification'),
               'client_secret' => $this->settings->get('secret'),
           ],
           'headers' => [
               'Accept' => 'application/json',
           ],
       ];
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        $cache = $this->settings->cache();
        /** @var ResponseInterface $response */
        $response = json_decode($carrierDataObject->response()->getBody()->getContents(), true, 512, JSON_OBJECT_AS_ARRAY);

        $token = Token::loadFromJson($response);

        $cache->set($this->settings->get('identification'), $token->accessToken());

        return $token->accessToken();
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        if ($carrierDataObject->error()) {
            throw AuthenticationException::forFailedAuthenticationResponse($carrierDataObject->error());
        }

        return $carrierDataObject->transaction();
    }
}
