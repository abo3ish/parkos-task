<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'arrival_date' => $this->faker->dateTime(),
            'departure_date' => $this->faker->dateTime(),
            'parking_id' => Parking::get()->random()->id,
            'status' => $this->faker->numberBetween(1, 2),
            'uuid' => $this->faker->numberBetween(10000, 200000),
        ];
    }
}
