<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Room_Img extends Model
{
    // Cambiamos 'rooms_img' por 'rooms_imgs' (con la S de la migración)
    protected $table = 'rooms_imgs'; 

    protected $fillable = ['room_type_id', 'img_url'];

    public $timestamps = false;

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(Room_Type::class, 'room_type_id'); 
    }
}
