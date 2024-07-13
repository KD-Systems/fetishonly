<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'bank_name',
        'account_name',
        'account_number',
        'swift_code',
        'paypal_email',
    ];
}
