<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class QueryPaymentTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_handles_an_approved_response()
    {
        $response = $this->service()->query('APPROVED');

        $this->assertEquals('approved', $response->state());
        $this->assertTrue($response->isApproved());
        $this->assertEquals(3458, $response->amount());
        $this->assertEquals('1614714969', $response->reference());
        $this->assertEquals('00', $response->reason());
    }

    /**
     * @test
     */
    public function it_handles_a_pending_response()
    {
        $response = $this->service()->query('PENDING1');

        $this->assertEquals('pending', $response->state());
        $this->assertTrue($response->isPending());
        $this->assertEquals(3458, $response->amount());
        $this->assertEquals('1614721251', $response->reference());
        $this->assertEquals('?-', $response->reason());
    }

    /**
     * @test
     */
    public function it_handles_a_rejected_response()
    {
        $response = $this->service()->query('REJECTED1');

        $this->assertEquals('rejected', $response->state());
        $this->assertTrue($response->isRejected());
        $this->assertEquals(3458, $response->amount());
        $this->assertEquals('1614721290', $response->reference());
        $this->assertEquals('54', $response->reason());
    }
}
