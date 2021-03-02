<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

class TransferIntentResponse
{
    protected $id;
    protected $code;
    protected $processUrl;
    /**
     * @var array
     */
    protected $meta;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->code = $data['code'];
        $this->processUrl = $data['processUrl'];
        $this->meta = $data['meta'] ?? [];
    }

    public function id(): string
    {
        return (string)$this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function processUrl(): string
    {
        return $this->processUrl;
    }

    public function meta(): array
    {
        return $this->meta;
    }

    public static function parseFromResponse(array $response): self
    {
        return new self([
            'meta' => $response['meta'],
            'id' => $response['data'][0]['header']['id'],
            'code' => $response['data'][0]['transferCode'],
            'processUrl' => $response['data'][0]['redirectURL'],
        ]);
    }
}
