<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityVerificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'identity_verification_id',
        'status',
        'verification_status',
        'response',
        'reason'
    ];
}
