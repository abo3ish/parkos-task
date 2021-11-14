<?php

namespace App\Http\Controllers;

use App\Events\ReservationBooked;
use Exception;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreReservation;
use App\Http\Resources\ReservationResource;
use App\Jobs\sendReservationPaidEmail;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::all();

        return response()->json([
            'reservations' => ReservationResource::collection($reservations),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReservation $request)
    {
        try {
            DB::beginTransaction();

            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'parking_id' => $request->parking_id,
                'arrival_date' => $request->arrival_date,
                'departure_date' => $request->arrival_date,
                'status' => Reservation::STATUS_PENDING,
                'uuid' => Reservation::getNextUUID()
            ]);

            ReservationBooked::dispatch($reservation);

            $reservationResource = new ReservationResource($reservation);

            DB::commit();

            return response()->json([
                'reservation' => $reservationResource,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);

            DB::rollBack();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        if (auth()->id() != $reservation->user_id) {
            return response()->json([
                'error' => 'You are not authorized to view this reservation'
            ], 403);
        }
        return response()->json([
            'reservation' => new ReservationResource($reservation)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        if (auth()->id() != $reservation->user_id) {
            return response()->json([
                'error' => 'You are not authorized to update this reservation'
            ], 403);
        }


        if ($reservation->status == Reservation::STATUS_PENDING && $request->status == Reservation::STATUS_PAID) {
            sendReservationPaidEmail::dispatch($reservation);

        }
        $reservation->update($request->all());


        return response()->json([
            'reservation' => new ReservationResource($reservation)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        if (auth()->id() != $reservation->user_id) {
            return response()->json([
                'error' => 'You are not authorized to delete this reservation'
            ], 403);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully'
        ]);
    }
}
