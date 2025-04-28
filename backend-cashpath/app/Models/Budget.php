<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
        'spent_amount',
        'status',
    ];

    public $incrementing = false; // 🔥 Important because it's UUID
    protected $keyType = 'string'; // 🔥 Important because it's UUID

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
