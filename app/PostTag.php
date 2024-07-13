<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'post_id'
    ];


    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
