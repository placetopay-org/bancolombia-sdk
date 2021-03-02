<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class HealtCallTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_handles_a_successful_check()
    {
        $response = $this->service()->health();
        $this->assertTrue($response->isHealthy());
    }
}
