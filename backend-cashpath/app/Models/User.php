<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅ Import Sanctum API Tokens
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasUuids; // ✅ Include HasApiTokens

    protected $fillable = [
        'name', 'email', 'password', 'profile_picture',
        'currency', 'language', 'default_account_id', 'timezone'
    ];

    protected $hidden = ['password', 'remember_token'];

    // Relationships
    public function accounts(): HasMany {
        return $this->hasMany(Account::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany {
        return $this->hasMany(Budget::class);
    }

    public function goals(): HasMany {
        return $this->hasMany(Goal::class);
    }

    public function notifications(): HasMany {
        return $this->hasMany(Notification::class);
    }
}
