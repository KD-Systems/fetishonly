<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'access_token',
        'refresh_token',
        'refreshed_at',
    ];
}
