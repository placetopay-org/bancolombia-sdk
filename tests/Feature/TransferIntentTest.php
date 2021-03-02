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

    /**
     * @test
     */
    public function it_handles_an_approved_query_response()
    {
        $result = $this->service()->query('_SQC5uKmF6L');

        $this->assertTrue($result->isApproved());
        $this->assertFalse($result->isPending());
        $this->assertFalse($result->isRejected());
        $this->assertEquals('approved', $result->state());
        $this->assertEquals('TRjJvCHT8qNj', $result->authorization());
        $this->assertEquals('2021-03-02T14:57:27-05:00', $result->date());
        $this->assertEquals('1614714969', $result->reference());
        $this->assertEquals(3458, $result->amount());
        $this->assertNull($result->description());
    }

    /**
     * @test
     */
    public function it_handles_a_pending_query_response()
    {
        $result = $this->service()->query('_SQC5uKPEND');

        $this->assertFalse($result->isApproved());
        $this->assertTrue($result->isPending());
        $this->assertFalse($result->isRejected());
        $this->assertEquals('pending', $result->state());
        $this->assertNull($result->authorization());
        $this->assertNull($result->date());
        $this->assertEquals('1614721251', $result->reference());
        $this->assertEquals(3458, $result->amount());
        $this->assertNull($result->description());
    }

    /**
     * @test
     */
    public function it_handles_a_rejected_query_response()
    {
        $result = $this->service()->query('_SQC5uKREJE');

        $this->assertFalse($result->isApproved());
        $this->assertFalse($result->isPending());
        $this->assertTrue($result->isRejected());
        $this->assertEquals('pending', $result->state());
        $this->assertNull($result->authorization());
        $this->assertNull($result->date());
        $this->assertEquals('1614721251', $result->reference());
        $this->assertEquals(3458, $result->amount());
        $this->assertEquals('Expired', $result->description());
    }
}
