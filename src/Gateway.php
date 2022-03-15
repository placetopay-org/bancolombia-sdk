<?php

namespace PlacetoPay\BancolombiaSDK;

use PlacetoPay\BancolombiaSDK\Parsers\HealtParser;
use PlacetoPay\Base\Constants\Operations;
use PlacetoPay\Base\Messages\FinancialTransaction;
use PlacetoPay\Flamingo\Exceptions\FlamingoException;
use PlacetoPay\Flamingo\Parsers\AuthorizationParser;
use PlacetoPay\Flamingo\Support\AuthenticationManager;
use PlacetoPay\Flamingo\Support\SettingsResolver;
use PlacetoPay\Tangram\Carriers\RestCarrier;
use PlacetoPay\Tangram\Contracts\Operations\AuthorizationContract;
use PlacetoPay\Tangram\Contracts\Operations\QueryContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;
use PlacetoPay\Tangram\Entities\BaseSettings;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;
use PlacetoPay\Tangram\Services\BaseGateway;

class Gateway extends BaseGateway implements AuthorizationContract, QueryContract
{
    protected BaseSettings $settings;
    protected RestCarrier $carrier;
    protected AuthenticationManager $authenticationManager;
    protected ?ParserHandlerContract $parser;

    /**
     * @throws InvalidSettingException
     */
    public function __construct(array $settings)
    {
        $this->settings = new BaseSettings($settings, SettingsResolver::create($settings));
        $this->carrier = new RestCarrier($this->settings->get('client'));
        $this->authenticationManager = new AuthenticationManager($this->carrier, $this->settings);
    }

    public function healt(): FinancialTransaction
    {
        $this->parser = new HealtParser($this->settings);

        return $this->carrier->request();
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

    public function authorizeTransaction(FinancialTransaction $transaction): FinancialTransaction
    {
        $this->parser = new AuthorizationParser($this->settings, $this->authenticationManager->getToken(), $this->cipher);

        return $this->financialTransaction(Operations::SALE, $transaction);
    }

    protected function process(string $operation, FinancialTransaction $transaction): FinancialTransaction
    {
        try {
            $carrierDataObject = $this->parseRequest($operation, $transaction, null);

            $this->carrier->request($carrierDataObject);

            $this->parseResponse($carrierDataObject);

            return $transaction;
        } catch (\Throwable $exception) {
            throw new FlamingoException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
