<?php

namespace Database\Factories;

use App\Models\Airport;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'section' => $this->faker->numberBetween(1, 10000),
            'address' => $this->faker->address,
            'airport_id' => Airport::inRandomOrder()->first()->id,
        ];
    }
}
