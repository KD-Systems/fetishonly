<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPostCategory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'category_id'];
}
