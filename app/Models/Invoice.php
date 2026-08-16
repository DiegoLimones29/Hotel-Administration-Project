<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'room_subtotal',
        'services_subtotal',
        'total_amount',
        'payment_method',
        'is_facturated',
    ];

    // Relación: La factura pertenece a una Reservación específica
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
