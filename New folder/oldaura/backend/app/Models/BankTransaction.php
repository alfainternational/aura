<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
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
        'bank_account_id',
        'wallet_id',
        'amount',
        'fee',
        'transaction_type', // deposit, withdrawal, payment
        'status', // pending, completed, failed, cancelled
        'reference_id',
        'external_reference',
        'description',
        'metadata',
        'transaction_date',
        'verification_code', // OTP para transacciones
        'verification_attempts',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'metadata' => 'json',
        'transaction_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bank associated with this transaction.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the bank account associated with this transaction.
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the wallet associated with this transaction.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Generate a new OTP verification code.
     */
    public function generateVerificationCode()
    {
        $this->verification_code = mt_rand(100000, 999999);
        $this->verification_attempts = 0;
        $this->save();
        
        return $this->verification_code;
    }

    /**
     * Verify the transaction with the given code.
     */
    public function verifyWithCode($code)
    {
        if ($this->verification_code == $code) {
            $this->verified_at = now();
            $this->status = 'completed';
            $this->save();
            return true;
        }
        
        $this->verification_attempts += 1;
        $this->save();
        return false;
    }

    /**
     * Check if the transaction requires OTP verification.
     */
    public function requiresOtpVerification()
    {
        // Verificar si la transacción requiere verificación OTP basado en el monto
        $otpThreshold = config('aura.otp_threshold', 1000); // Valor predeterminado 1000
        
        return $this->amount >= $otpThreshold && !$this->verified_at;
    }
}
