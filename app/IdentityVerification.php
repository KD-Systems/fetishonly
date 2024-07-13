<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "uuid",
        "public_url",
        "qr_code",
        "status",
    ];
}
