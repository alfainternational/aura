<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'swift_code',
        'logo',
        'api_key',
        'api_secret',
        'api_endpoint',
        'webhook_url',
        'is_active',
        'integration_type',
        'settings'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    /**
     * Get the bank accounts for this bank.
     */
    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get the transactions for this bank.
     */
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }
}
