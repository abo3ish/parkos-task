<?php

namespace App\Models;

use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function parkings()
    {
        return $this->hasMany(Parking::class);
    }

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Parking::class);
    }
}
