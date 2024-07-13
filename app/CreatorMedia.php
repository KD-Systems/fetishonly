<?php

namespace App;

use App\Model\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatorMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachment_id',
        'creator_id',
    ];

    public function attachment() {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
