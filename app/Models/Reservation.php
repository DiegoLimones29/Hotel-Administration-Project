<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'room_id',
        'user_id',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'num_nights',
        'total_cost',
        'status',
        'cancellation_reason',
        'actual_check_in_at',   
        'actual_check_out_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'reservation_id');
    }

    public function reservationServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReservationService::class, 'reservation_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
