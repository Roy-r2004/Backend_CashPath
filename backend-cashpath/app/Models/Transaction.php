<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'category_id',
        'amount',
        'type',
        'date',
        'time',
        'note',
        'receipt_image',
        'is_recurring',
    ];

    /**
     * ✅ Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Relationship with Account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * ✅ Relationship with Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
