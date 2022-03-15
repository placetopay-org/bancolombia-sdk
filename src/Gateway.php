<?php

namespace PlacetoPay\BancolombiaSDK;

use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\BancolombiaSDK\Exceptions\ErrorResponseException;
use PlacetoPay\BancolombiaSDK\Parsers\HealtParser;
use PlacetoPay\Base\Constants\Operations;
use PlacetoPay\Base\Messages\FinancialTransaction;
use PlacetoPay\Tangram\Carriers\RestCarrier;
use PlacetoPay\Tangram\Contracts\Operations\QueryContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;
use PlacetoPay\Tangram\Services\BaseGateway;

class Gateway extends BaseGateway implements QueryContract
{
    protected Settings $settings;
    protected RestCarrier $carrier;
    protected ?ParserHandlerContract $parser;

    /**
     * @throws InvalidSettingException
     */
    public function __construct(array $settings)
    {
        $this->settings = new Settings($settings, null);
        $this->carrier = new RestCarrier($this->settings->get('client'));
    }

    public function healt(FinancialTransaction $transaction): FinancialTransaction
    {
        /** @var HealtParser parser */
        $this->parser = new HealtParser($this->settings);

        return $this->process(Operations::QUERY, $transaction);
    }

    public function query(FinancialTransaction $transaction): FinancialTransaction
    {
        return $this->process(Operations::QUERY, $transaction);
    }

    public function request(FinancialTransaction $transaction): FinancialTransaction
    {
        return $this->process(Operations::QUERY, $transaction);
    }

    public function handleCallback(FinancialTransaction $transaction): FinancialTransaction
    {
        return $this->process(Operations::QUERY, $transaction);
    }

    protected function process(string $operation, FinancialTransaction $transaction): FinancialTransaction
    {
        try {
            $carrierDataObject = $this->parseRequest($operation, $transaction);

            $this->carrier->request($carrierDataObject);

            $this->parseResponse($carrierDataObject);

            return $transaction;
        } catch (\Throwable $exception) {
            throw new ErrorResponseException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
