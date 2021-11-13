<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    public $airports = [
        ['name' => 'Cairo International Airport '],
        ['name' => 'O.R. Tambo International Airport'],
        ['name' => 'Cape Town International Airport'],
        ['name' => 'Murtala Muhammed International Airport '],
        ['name' => 'Nnamdi Azikiwe International Airport '],
        ['name' => 'Mohammed V International Airport '],
        ['name' => 'King Shaka International Airport '],
        ['name' => 'Hurghada International Airport '],
        ['name' => 'Sharm el-Sheikh International Airport '],
        ['name' => 'Ameland Airport '],
        ['name' => 'Amsterdam Airport Schiphol '],
        ['name' => 'Terlet Airfield '],
        ['name' => 'Flamingo International Airport '],
        ['name' => 'FKempen Airport'],
        ['name' => 'Den Helder Airport'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->airports as $airport) {
            Airport::create($airport);
        }
    }
}
