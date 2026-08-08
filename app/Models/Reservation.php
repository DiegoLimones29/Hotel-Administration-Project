<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'estimated_check_in',
        'estimated_check_out',
        'actual_check_in',
        'actual_check_out',
        'status',
        'origin',
        'price_per_night_charged',
        'total_room_amount',
    ];

    // Relación: La reservación pertenece a un Huésped (Usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: La reservación pertenece a una Habitación
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relación: Una reservación puede tener muchos servicios asociados (Tabla pivote)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'reservation_service')
                    ->withPivot('quantity', 'price_charged')
                    ->withTimestamps();
    }

    // Relación: Una reservación genera una Factura final
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
