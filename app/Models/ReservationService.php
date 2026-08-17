<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationService extends Model
{
    protected $table = 'reservation_services';

    protected $fillable = [
        'reservation_id',
        'service_id',
        'quantity',
        'unit_price',
        'requested_date',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}