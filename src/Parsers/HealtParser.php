<?php

namespace PlacetoPay\BancolombiaSDK\Parsers;

use GuzzleHttp\Exception\BadResponseException;
use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;
use PlacetoPay\Tangram\Entities\BaseSettings;

class HealtParser implements ParserHandlerContract
{
    private BaseSettings $settings;
    private string $token;

    public function __construct(Settings $settings, string $token)
    {
        $this->settings = $settings;
        $this->token = $token;
    }

    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/vnd.bancolombia.v1+json',
                'Content-Type' => 'application/vnd.bancolombia.v1+json',
            ],
        ];
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        return $carrierDataObject->response()->status()->isSuccessful();
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        // TODO: Implement errorHandler() method.
    }
}
