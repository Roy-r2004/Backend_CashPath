<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model {
    use HasFactory, HasUuids;

    protected $fillable = [
        'id', 'user_id', 'name', 'balance', 'account_type',
        'currency', 'icon', 'is_default'
    ];

    protected $casts = [
        'balance' => 'float', // ✅ Ensure balance is always a float
        'is_default' => 'boolean', // ✅ Ensure correct boolean casting
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}
