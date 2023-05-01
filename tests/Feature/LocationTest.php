<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_available(): void
    {
        $response = $this->get('/api/location');

        $response->assertStatus(200);
    }

    public function test_availableOne(): void
    {
        $response = $this->get('/api/location/rossiya');

        $response->assertStatus(200);
    }
}
