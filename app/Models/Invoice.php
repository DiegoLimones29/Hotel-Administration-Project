<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'reservation_id',
        'room_cost',
        'services_cost',
        'total_cost',
        'payment_method',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
