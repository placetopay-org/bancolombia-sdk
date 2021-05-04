<?php

namespace Tests\Feature;

use PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException;
use Tests\BaseTestCase;

class RequestPaymentTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_validates_return_url()
    {
        $this->expectException(BancolombiaException::class);
        $this->service()->request([
            'reference' => '123456789',
            'description' => 'Pago de prueba 1',
            'amount' => 15000,
            'returnUrl' => 'BAD_URL',
            'confirmationUrl' => 'https://www.valid.com',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_confirmation_url()
    {
        $this->expectException(BancolombiaException::class);
        $this->service()->request([
            'reference' => '123456789',
            'description' => 'Pago de prueba 1',
            'amount' => 15000,
            'returnUrl' => 'https://www.valid.com',
            'confirmationUrl' => 'INVALID_URL',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_basic_amount()
    {
        $this->expectException(BancolombiaException::class);
        $this->service()->request([
            'reference' => '123456789',
            'description' => 'Pago de prueba 1',
            'amount' => 0,
            'returnUrl' => 'https://www.valid.com',
            'confirmationUrl' => 'https://www.valid.com',
        ]);
    }

    /**
     * @test
     */
    public function it_handles_a_correct_response()
    {
        $response = $this->service()->request([
            'reference' => '123456789',
            'description' => 'Pago de prueba 1',
            'amount' => 15000,
            'returnUrl' => 'https://www.valid.com',
            'confirmationUrl' => 'https://www.valid.com',
        ]);

        $this->assertEquals('_SQC5uKmF6L', $response->id());
        $this->assertNotFalse(filter_var($response->processUrl(), FILTER_VALIDATE_URL));
    }
}
