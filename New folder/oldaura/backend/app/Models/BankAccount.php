<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'bank_id',
        'account_number',
        'account_name',
        'is_primary',
        'is_verified',
        'last_verified_at',
        'status',
        'meta_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'last_verified_at' => 'datetime',
        'meta_data' => 'json',
    ];

    /**
     * Get the user that owns the bank account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bank that this account belongs to.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the transactions for this bank account.
     */
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }
}
