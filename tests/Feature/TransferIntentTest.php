<?php

namespace Tests\Feature;

use PlacetoPay\BancolombiaSDK\Exceptions\ErrorResponseException;
use Tests\BaseTestCase;

class TransferIntentTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_handles_a_successful_response()
    {
        $response = $this->service()->request([
            'reference' => 123456789,
            'description' => 'Pago de prueba',
            'amount' => 321.78,
            'returnUrl' => 'https://dnetix.co/ping/return?transferCode=something',
            'confirmationUrl' => 'https://dnetix.co/ping/callback_b',
        ]);

        $this->assertEquals('https://sandbox-boton-dev.apps.ambientesbc.com/web/transfer-gateway/checkout/_SQC5uKmF6L', $response->processUrl());
        $this->assertEquals('_SQC5uKmF6L', $response->code());
        $this->assertEquals('_SQC5uKmF6L', $response->id());
    }

    /**
     * @test
     */
    public function it_handles_a_service_unavailable_response()
    {
        $this->expectException(ErrorResponseException::class);
        $this->expectExceptionMessage('Error leyendo la peticion del servidor {"status":503,"title":"Service Unavailable"}');
        $this->expectExceptionCode(500);

        $this->service()->request([
            'reference' => 'UNAVAILABLE',
            'description' => 'Pago de prueba',
            'amount' => 321.78,
            'returnUrl' => 'https://dnetix.co/ping/return',
            'confirmationUrl' => 'https://dnetix.co/ping/callback',
        ]);
    }
}
