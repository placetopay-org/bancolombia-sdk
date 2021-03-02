<?php

namespace PlacetoPay\BancolombiaSDK\Helpers;

use GuzzleHttp\Exception\BadResponseException;
use PlacetoPay\BancolombiaSDK\Entities\Settings;
use PlacetoPay\BancolombiaSDK\Entities\Token;
use PlacetoPay\BancolombiaSDK\Entities\TransferIntentRequest;
use PlacetoPay\BancolombiaSDK\Exceptions\AuthenticationException;
use PlacetoPay\BancolombiaSDK\Exceptions\ErrorResponseException;

class Carrier
{
    public static function authentication(Settings $settings): string
    {
        $cache = $settings->cache();

        if ($result = $cache->get($settings->identification())) {
            $token = Token::loadFromJson($result);
            if ($token->isActive()) {
                return $token->accessToken();
            }
        }

        try {
            $response = $settings->client()->post($settings->authUrl(), [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'Transfer-Intention:write:app Transfer-Intention:read:app',
                    'redirect_uri' => 'https://dnetix.co/ping/auth_call',
                    'client_id' => $settings->identification(),
                    'client_secret' => $settings->secret(),
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $content = $response->getBody()->getContents();
            $token = Token::loadFromJson($content);

            $cache->set($settings->identification(), $content);
        } catch (BadResponseException $e) {
            throw AuthenticationException::forFailedAuthenticationResponse($e);
        }

        return $token->accessToken();
    }

    public static function requestCall(Settings $settings, TransferIntentRequest $parameters): array
    {
        $token = self::authentication($settings);

        try {
            $response = $settings->client()->post($settings->serviceUrl('transfer/action/registry'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/vnd.bancolombia.v1+json',
                    'Content-Type' => 'application/vnd.bancolombia.v1+json',
                ],
                'json' => $parameters->asRequest(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (BadResponseException $e) {
            throw ErrorResponseException::fromResponse(
                json_decode($e->getResponse()->getBody()->getContents(), true)
            );
        }
    }

    public static function healthCall(Settings $settings): bool
    {
        $token = self::authentication($settings);

        try {
            $response = $settings->client()->head($settings->serviceUrl('bancolombia-button-transference-management/health'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/vnd.bancolombia.v1+json',
                    'Content-Type' => 'application/vnd.bancolombia.v1+json',
                ],
            ]);
            return $response->getStatusCode() == 200;
        } catch (BadResponseException $e) {
            return false;
        }
    }
}
