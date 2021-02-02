<?php

/**
 * Status: Done!
 */

namespace Tests\Feature;

use Tests\TestCase;

class ApiTokenMiddleware extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        echo PHP_EOL;
        //empty
        $this->assertStringContainsString($this->testToken(null), 'Empty token given');
        //short
        $this->assertStringContainsString($this->testToken(123), 'Invalid token given');
        //not found
        $this->assertStringContainsString(
            $this->testToken('si0Z9bZrD8JPqVLYeRZ5FI5DDCaLPhRu4WWVp22JVErWeh0Lssx64xPcQ3UlB6F4qqONjascVzEes345'),
            'Token not found'
        );
        //exists
        $this->assertStringContainsString(
            $this->testToken('si0Z9bZrD8JPqVLYeRZ5FI5DDCaLPhRu4WWVp22JVErWeh0Lssx64xPcQ3UlB6F4qqONjascVzEeskVA'),
            'Invalid company\'s INN given!'
        );
    }

    private function testToken($token) : string
    {
        $response = $this->call('GET', 'http://127.0.0.1:8001/api/v1/companies/123',
            [
                'inn' => 123,
                'token' => $token
            ]
        );
        return $response->offsetGet('message');
    }
}
