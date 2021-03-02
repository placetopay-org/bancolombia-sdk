<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

class Token
{
    protected $accessToken;
    protected $scope;
    protected $consentedOn;
    protected $expiresIn;

    public function __construct($accessToken, $scope, $consentedOn, $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->scope = $scope;
        $this->consentedOn = $consentedOn;
        $this->expiresIn = $expiresIn;
    }

    public function accessToken(): string
    {
        return $this->accessToken ?? '';
    }

    public function isActive(): bool
    {
        if ($this->consentedOn && $this->expiresIn) {
            // TODO CHECK DATES
            return true;
        }

        return false;
    }

    public static function loadFromJson(string $tokenContent): self
    {
        $data = json_decode($tokenContent);
        return new self($data->access_token, $data->scope, $data->consented_on, $data->expires_in);
    }
}
