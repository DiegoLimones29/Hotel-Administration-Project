<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'country_of_origin',
        'phone_number',
    ];

    // Relación: Un detalle de huésped pertenece a un Usuario único
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
