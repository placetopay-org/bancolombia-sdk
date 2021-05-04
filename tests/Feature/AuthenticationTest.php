<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class AuthenticationTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_handles_correctly_the_authentication()
    {
        $response = $this->service()->health();
        $this->assertTrue($response->isHealthy());
    }
}
