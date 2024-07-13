<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    public function categoryPost() {
        return $this->hasMany(UserPostCategory::class, 'category_id', 'id');
    }
}
