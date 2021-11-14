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

    protected $casts = [
        'status' => 'integer',
        'uuid' => 'integer'
    ];

    public static function getLatestUUID()
    {
        return self::orderBy('id', 'desc')->first()->uuid ?? 1000;
    }

    public static function getNextUUID()
    {
        return self::getLatestUUID() + 1;
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
