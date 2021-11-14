<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Airport;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Jobs\sendReservationPaidEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->seed([Airport::class]);
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_customer_can_see_reservations()
    {
        Parking::factory()->count(3)->create();

        Reservation::factory()
            ->count(10)
            ->for($this->user)
            ->create();

        $response = $this->actingAs($this->user)->get('/reservations');

        $response = $this->get('/reservations');

        $response->assertStatus(200);
    }

    public function test_job_dispatched()
    {

        Parking::factory()->count(3)->create();

        Reservation::factory()
            ->count(10)
            ->for($this->user)
            ->create();

        $this->user->reservations()->first()->update([
            'status' => Reservation::STATUS_PAID
        ]);

        Mail::assertQueued(sendReservationPaidEmail::class);

        $response = $this->get('/reservations');

        $response->assertStatus(200);
    }
}
