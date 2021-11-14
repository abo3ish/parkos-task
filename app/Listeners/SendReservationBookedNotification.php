<?php

namespace App\Listeners;

use App\Mail\ReservationPaid;
use App\Events\ReservationBooked;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReservationBookedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // send notification to other systems.
        Mail::to($event->reservation->user->email)
            ->send(new ReservationPaid());
    }
}
