<?php

namespace Tests\Feature;

use PlacetoPay\BancolombiaSDK\BancolombiaButton;
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
        $this->assertEquals('rejected', $result->state());
        $this->assertNull($result->authorization());
        $this->assertNull($result->date());
        $this->assertEquals('1614721290', $result->reference());
        $this->assertEquals(3458, $result->amount());
        $this->assertEquals('Expired', $result->description());
    }

    /**
     * @test
     */
    public function it_handles_a_sign_validation_correctly()
    {
        // This information is posted to the callback URL so make it an array
        $posted = [
            'transferVoucher' => 'TRqjDhF6DpIF',
            'transferAmount' => '3458.00',
            'transferStateDescription' => 'null',
            'sign' => '8f0e15f62156cb3921b4e73f95fcb6a855eca332e1da26b994c5f55fa134129cdb40b49f8489b2cc63b7798f8baf84ea555a84cd1da4648700d3ce3c57da1838',
            'requestDate' => '2021-03-02T16:58:26.341-0500',
            'transferState' => 'approved',
            'transferDate' => '2021-03-02T16:58:26.000-0500',
            'transferCode' => '_fmN2Gb1n3C',
            'transferReference' => '1614722266',
            'commerceTransferButtonId' => 'h4ShG3NER1C',
        ];

        // Still not sure if is the same application secret, but this way it will work either case
        $result = BancolombiaButton::handleCallback($posted, '5Fj8eK4rlyUd252L48herdrnEO');

        $this->assertTrue($result->isApproved());
        $this->assertFalse($result->isPending());
        $this->assertFalse($result->isRejected());
        $this->assertEquals('approved', $result->state());
        $this->assertEquals('TRqjDhF6DpIF', $result->authorization());
        $this->assertEquals('2021-03-02T16:58:26-05:00', $result->date());
        $this->assertEquals('1614722266', $result->reference());
        $this->assertEquals(3458, $result->amount());
        $this->assertNull($result->description());
    }
}
