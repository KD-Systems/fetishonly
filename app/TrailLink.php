<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'name',
        'limit',
        'expire_at',
        'duration',
    ];

    protected $casts = [
        'expire_at' => 'datetime'
    ];

    protected $with = ['trailLog'];

    public function trailLog() {
        return $this->hasMany(TrailLog::class, 'trail_links_id', 'id');
    }
}
