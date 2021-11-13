<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AirportSeeder::class
        ]);

        User::factory()->count(50)->create();
        Parking::factory()->count(50)->create();
        Reservation::factory()->count(500)->create();
    }
}
