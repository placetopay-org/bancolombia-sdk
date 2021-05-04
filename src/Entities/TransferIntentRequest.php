<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

use PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException;

class TransferIntentRequest
{
    protected $hash;
    protected $reference;
    protected $description;
    protected $amount;
    protected $returnUrl;
    protected $confirmationUrl;

    public function __construct(array $data, string $hash)
    {
        $this->hash = $hash;
        $this->reference = $data['reference'];
        $this->description = $data['description'];
        $this->amount = $data['amount'];
        $this->returnUrl = $data['returnUrl'];
        $this->confirmationUrl = $data['confirmationUrl'];

        if ($this->amount <= 0) {
            throw BancolombiaException::forInvalidRequest('amount');
        }

        if (!filter_var($this->returnUrl, FILTER_VALIDATE_URL)) {
            throw BancolombiaException::forInvalidRequest('returnUrl');
        }

        if (!filter_var($this->confirmationUrl, FILTER_VALIDATE_URL)) {
            throw BancolombiaException::forInvalidRequest('confirmationUrl');
        }
    }

    public function asRequest(): array
    {
        // TODO Validar 2 decimales max
        return [
            'data' => [
                [
                    'commerceTransferButtonId' => (string)$this->hash,
                    'transferReference' => (string)$this->reference,
                    'transferAmount' => (float)$this->amount,
                    'commerceUrl' => $this->returnUrl,
                    'confirmationURL' => $this->confirmationUrl,
                    'transferDescription' => $this->description,
                ],
            ],
        ];
    }
}
