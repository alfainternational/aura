<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'installment_plan_id',
        'amount',
        'due_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'status', // pending, paid, overdue, defaulted
        'installment_number',
        'reminder_sent_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the installment plan that owns this installment.
     */
    public function installmentPlan()
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    /**
     * Mark this installment as paid.
     */
    public function markAsPaid($paymentMethod, $paymentReference)
    {
        $this->status = 'paid';
        $this->paid_date = now();
        $this->payment_method = $paymentMethod;
        $this->payment_reference = $paymentReference;
        $this->save();
        
        // Verificar si todas las cuotas están pagadas para actualizar el plan
        $plan = $this->installmentPlan;
        $pendingInstallments = $plan->installments()->where('status', '!=', 'paid')->count();
        
        if ($pendingInstallments === 0) {
            $plan->update(['status' => 'completed']);
        }
        
        return $this;
    }

    /**
     * Check if the installment is overdue.
     */
    public function checkOverdue()
    {
        if ($this->status === 'pending' && $this->due_date < now()) {
            $this->status = 'overdue';
            $this->save();
            
            return true;
        }
        
        return false;
    }

    /**
     * Send a reminder for this installment.
     */
    public function sendReminder()
    {
        if ($this->status === 'pending' || $this->status === 'overdue') {
            // Aquí se implementaría la lógica para enviar notificaciones
            
            $this->reminder_sent_at = now();
            $this->save();
            
            return true;
        }
        
        return false;
    }
}
