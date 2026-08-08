<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    // Relación inversa: Un servicio puede estar en muchas reservaciones
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_service')
                    ->withPivot('quantity', 'price_charged')
                    ->withTimestamps();
    }
}
