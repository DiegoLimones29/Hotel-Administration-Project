<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Room extends Model
{
    protected $table= 'rooms'; 

    protected $fillable= ['room_type_id', 'room_number', 'room_floor', 'state']; 

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(Room_Type::class, 'room_type_id'); 
    }

}
