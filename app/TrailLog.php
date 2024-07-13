<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'trail_links_id'
    ];
}
