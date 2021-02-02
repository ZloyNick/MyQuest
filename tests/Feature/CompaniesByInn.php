<?php

/**
 * Status: Done!
 */

namespace Tests\Feature;

use Tests\TestCase;

class CompaniesByInn extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertStringContainsString($this->testInn(123), 'Invalid company\'s INN given');
        $this->assertIsArray($this->testInn(2310031475));
    }

    private function testInn($inn)
    {
        $response = $this->call('GET', 'http://127.0.0.1:8000/api/v1/companies/'.$inn,
            [
                'token' => 'si0Z9bZrD8JPqVLYeRZ5FI5DDCaLPhRu4WWVp22JVErWeh0Lssx64xPcQ3UlB6F4qqONjascVzEeskVA'
            ]
        );
        if($response->offsetExists('message'))
            return $response->offsetGet('message');
        else return $response->offsetGet('companies');
    }
}
