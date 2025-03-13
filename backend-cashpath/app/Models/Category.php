<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model {
    use HasFactory,HasUuids;

    protected $fillable = ['id','user_id', 'name', 'type', 'icon', 'color', 'parent_id'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany {
        return $this->hasMany(Budget::class);
    }

    public function parentCategory(): BelongsTo {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories(): HasMany {
        return $this->hasMany(Category::class, 'parent_id');
    }
}

