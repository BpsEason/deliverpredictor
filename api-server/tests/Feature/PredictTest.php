<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PredictTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_the_predict_endpoint_returns_a_valid_response(): void
    {
        $response = $this->postJson('/api/predict', [
            'past_late_count' => 5,
            'leave_frequency' => 2,
            'avg_delivery_time' => 15.5,
            'rating' => 4.2
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'courier_id',
            'risk_score',
            'recommend_replacement'
        ]);
    }
}
