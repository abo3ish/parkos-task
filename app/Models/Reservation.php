<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 1;
    public const STATUS_PAID = 2;

    protected $fillable = [
        'uuid',
        'user_id',
        'parking_id',
        'arrival_date',
        'departure_date',
        'status',
    ];

    protected $dates = [
        'arrival_date',
        'departure_date',
    ];
}
