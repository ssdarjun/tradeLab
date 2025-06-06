<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manipulation extends Model
{
    use HasFactory;

    protected $table = 'manipulations'; // Optional, Laravel will use this by default

    protected $fillable = [
        'crypto_id',
        'start_time',
        'end_time',
        'prediction_override',
        'min',
        'max',
        'current', // Current price of the crypto during manipulation
    ];

    // Example relation:
    public function crypto()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_id');
    }
}
