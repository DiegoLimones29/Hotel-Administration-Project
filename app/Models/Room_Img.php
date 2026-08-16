<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Room_Img extends Model
{
    // Forzamos el nombre exacto con 'S' de la migración de Supabase
    protected $table = 'rooms_imgs'; 

    protected $fillable = ['room_type_id', 'img_url'];

    // Desactivamos timestamps porque tu migración no tiene created_at ni updated_at
    public $timestamps = false;

    /**
     * Relación: Una imagen pertenece a un tipo de habitación
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(Room_Type::class, 'room_type_id'); 
    }
}
