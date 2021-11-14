<?php

namespace App\Models;

use App\Models\Airport;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'address',
        'airport_id'
    ];

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
