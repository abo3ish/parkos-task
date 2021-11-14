<?php

namespace App\Http\Resources;

use App\Http\Resources\ParkingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'arrival_date' => $this->arrival_date,
            'departure_date' => $this->departure_date,
            'parking' =>  new ParkingResource($this->parking),
        ];
    }
}
