<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Room extends Model
{
    protected $table= 'rooms'; 

    protected $fillable= ['room_type_id', 'room_number', 'room_floor', 'state']; 

   
// Relación: Una habitación pertenece a un tipo de habitación específico
public function roomType()
{
    // Usamos 'room_type_id' porque así nombraron la llave foránea tus compañeros
    return $this->belongsTo(RoomType::class, 'room_type_id');
}

}
