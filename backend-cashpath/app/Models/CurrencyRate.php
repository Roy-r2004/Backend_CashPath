<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model {
    use HasFactory;

    protected $fillable = ['base_currency', 'target_currency', 'exchange_rate'];
}
