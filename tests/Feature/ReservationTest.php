<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Airport;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Queue\Jobs\Job;
use App\Events\ReservationBooked;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendReservationPaidEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Listeners\SendReservationBookedNotification;
use App\Mail\ReservationPaid;

class ReservationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_customer_can_see_reservations()
    {
        Parking::factory()->count(10)->create();

        Reservation::factory()
            ->count(10)
            ->for($this->user)
            ->create();

       $this->actingAs($this->user);

       $this->user->reservations()->get();

        $this->get('/api/reservations')
        ->assertStatus(200)
        ->assertJsonCount(10, 'reservations')
        ->assertJsonStructure([
            'reservations' => [
                [
                    'id',
                    'uuid' ,
                    'arrival_date' ,
                    'departure_date',
                    'parking' => [
                        'id' ,
                        'section',
                        'airport' => [
                            'id',
                            'name',
                        ],
                    ]
                ]
            ]
        ]);
    }

    public function test_user_can_reserve_a_parking()
    {
        Parking::factory()->count(3)->create();

        $this->actingAs($this->user);

        $response = $this->postJson('/api/reservations/', [
            'user_id' => $this->user->id,
            'arrival_date' => '02-02-2021 10:10',
            'departure_date' => '03-02-2021 10:10',
            'parking_id' => Parking::get()->random()->id,
            'status' => $this->faker->numberBetween(1, 2),
            'uuid' => $this->faker->numberBetween(10000, 200000),
        ]);

        $response->assertStatus(200);
    }

    public function test_email_job_dispatched()
    {
        Bus::fake([
            SendReservationPaidEmail::class
        ]);

        Reservation::factory()
            ->count(10)
            ->for($this->user)
            ->create();

        $this->actingAs($this->user);

        $reservation = $this->user->reservations()->where('status', Reservation::STATUS_PENDING)->first();

        $this->putJson('api/reservations/' . $reservation->id, [
            'status' => 2
        ])->assertStatus(200);

        Bus::assertDispatched(SendReservationPaidEmail::class);

        $response = $this->getJson('/api/reservations/' . $reservation->id);

        $response->assertJson([
            'reservation' => [
                'id' => $reservation->id,
                'status' => 2,
            ]
        ])
        ->assertStatus(200);
    }

    public function test_email_is_sent_to_customer_when_paid()
    {
        Mail::fake();

        Parking::factory()->count(3)->create();

        Reservation::factory()
            ->for($this->user)
            ->create([
                'arrival_date' => $this->faker->dateTime(),
                'departure_date' => $this->faker->dateTime(),
                'parking_id' => Parking::get()->random()->id,
                'status' => Reservation::STATUS_PENDING,
                'uuid' => $this->faker->numberBetween(10000, 200000),
            ]);

        $this->actingAs($this->user);

        $reservation = $this->user->reservations()->where('status', Reservation::STATUS_PENDING)->first();

        $this->putJson('api/reservations/' . $reservation->id, [
            'status' => 2
        ])->assertStatus(200);

        Mail::assertSent(ReservationPaid::class);

        $response = $this->getJson('/api/reservations/' . $reservation->id);

        $response
            ->assertJson([
            'reservation' => [
                'id' => $reservation->id,
                'status' => 2,
            ]
        ])
        ->assertStatus(200);
    }

    public function test_dispatch_event_when_create_reservations()
    {
        Event::fake();

        Parking::factory()->count(3)->create();

        $this->actingAs($this->user);

        $response = $this->postJson('/api/reservations/', [
            'user_id' => $this->user->id,
            'arrival_date' => '02-02-2021 10:10',
            'departure_date' => '03-02-2021 10:10',
            'parking_id' => Parking::get()->random()->id,
            'status' => $this->faker->numberBetween(1, 2),
            'uuid' => $this->faker->numberBetween(10000, 200000),
        ]);

        Event::assertDispatched(ReservationBooked::class);

        Event::assertListening(
            ReservationBooked::class,
            SendReservationBookedNotification::class
        );

        $response->assertStatus(200);
    }

    public function test_reservation_paid_job_is_queued()
    {
        $this->withoutExceptionHandling();

        Queue::fake([
            SendReservationPaidEmail::class
        ]);

        Parking::factory()->count(3)->create();

        Reservation::factory()
            ->for($this->user)
            ->create([
                'arrival_date' => $this->faker->dateTime(),
                'departure_date' => $this->faker->dateTime(),
                'parking_id' => Parking::get()->random()->id,
                'status' => Reservation::STATUS_PENDING,
                'uuid' => $this->faker->numberBetween(10000, 200000),
            ]);

        $this->actingAs($this->user);

        $reservation = $this->user->reservations()->where('status', Reservation::STATUS_PENDING)->first();

        $this->putJson('api/reservations/' . $reservation->id, [
            'status' => 2
        ])->assertStatus(200);

        Queue::assertPushed(SendReservationPaidEmail::class);

        $response = $this->getJson('/api/reservations/' . $reservation->id);

        $response
            ->assertJson([
                'reservation' => [
                    'id' => $reservation->id,
                    'status' => 2,
                ]
            ])
            ->assertStatus(200);
    }
}
