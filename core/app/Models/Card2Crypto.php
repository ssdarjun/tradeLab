<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Card2Crypto extends Model
{
    protected $table = 'card2crypto';

     protected $fillable = [
        'wallet_address',
        'rate',
        'min_amount',
        'max_amount',
        'fixed_charge',
        'percent_charge',
    ];

     protected $casts = [
        'rate' => 'array',
    ];

}
