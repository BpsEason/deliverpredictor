<?php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Courier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'past_late_count' => $this->faker->numberBetween(0, 10),
            'leave_frequency' => $this->faker->numberBetween(0, 5),
            'avg_delivery_time' => $this->faker->randomFloat(2, 10, 30),
            'rating' => $this->faker->randomFloat(1, 3.0, 5.0),
        ];
    }
}
