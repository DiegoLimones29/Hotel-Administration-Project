<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room_Type extends Model
{
    protected $table = 'room_type'; 

    public function rooms(): HasMany
    {
        return $this-> hasMany(Room::class, 'room_type_id');
    }

    public function images(): HasMany
    {
        return $this-> hasMany(Room_Img::class, 'room_type_id'); 
    }

}
