<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Room_Img extends Model
{
    protected $table= 'rooms_img'; 

    protected $fillable= ['room_type_id', 'img_url'];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(Room_Type::class, 'room_type_id'); 
    }
}
