<?php

namespace PlacetoPay\BancolombiaSDK\Parsers;

use GuzzleHttp\Exception\BadResponseException;
use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;
use PlacetoPay\Tangram\Entities\BaseSettings;

class HealtParser implements ParserHandlerContract
{
    private BaseSettings $settings;

    public function __construct(BaseSettings $settings)
    {
        $this->settings = $settings;
    }

    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        $token = $this->settings->get('token');

        try {
            $response = $settings->client()->head($settings->serviceUrl('bancolombia-button-transference-management/health'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/vnd.bancolombia.v1+json',
                    'Content-Type' => 'application/vnd.bancolombia.v1+json',
                ],
            ]);
            $response->getStatusCode() == 200;
        } catch (BadResponseException $e) {
        }

        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.bancolombia.v1+json',
                'Content-Type' => 'application/vnd.bancolombia.v1+json',
            ],
        ];
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        // TODO: Implement parserResponse() method.
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        // TODO: Implement errorHandler() method.
    }
}
