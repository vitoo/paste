<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paste extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'slug', 'expired_at'];

    //cast
    protected $casts = [
        'expired_at' => 'datetime',
    ];

}
