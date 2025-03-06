<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'merchant_id',
        'total_amount',
        'installment_amount',
        'number_of_installments',
        'frequency', // monthly
        'interest_rate',
        'start_date',
        'end_date',
        'status', // active, completed, defaulted, cancelled
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the order associated with this installment plan.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who created this installment plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the merchant associated with this installment plan.
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Get the installments for this plan.
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * Create installment records for this plan.
     */
    public function generateInstallments()
    {
        $startDate = $this->start_date;
        $amount = $this->installment_amount;
        
        for ($i = 1; $i <= $this->number_of_installments; $i++) {
            $dueDate = clone $startDate;
            $dueDate->addMonths($i - 1);
            
            $this->installments()->create([
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => $i === 1 ? 'paid' : 'pending', // Primera cuota pagada al inicio
                'installment_number' => $i,
            ]);
        }
    }

    /**
     * Calculate the total amount with interest.
     */
    public static function calculateTotalWithInterest($baseAmount, $numberOfInstallments)
    {
        // Interés del 5% solo si son 12 cuotas
        $interestRate = $numberOfInstallments == 12 ? 0.05 : 0;
        $totalAmount = $baseAmount * (1 + $interestRate);
        
        return [
            'total_amount' => $totalAmount,
            'installment_amount' => $totalAmount / $numberOfInstallments,
            'interest_rate' => $interestRate,
        ];
    }

    /**
     * Get the status of the installment plan.
     */
    public function getStatusAttribute($value)
    {
        // Si todas las cuotas están pagadas, actualizar estado a completado
        if ($value !== 'completed' && $this->installments()->where('status', '!=', 'paid')->count() === 0) {
            $this->update(['status' => 'completed']);
            return 'completed';
        }
        
        return $value;
    }
}
