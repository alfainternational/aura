<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\User;

class TransactionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب جميع المعاملات الموجودة
        $transactions = Transaction::all();
        
        foreach ($transactions as $transaction) {
            // إنشاء سجل إنشاء المعاملة
            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'action' => 'created',
                'description' => 'تم إنشاء المعاملة',
                'metadata' => [
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                ]
            ]);
            
            // إذا كانت المعاملة مكتملة أو ملغاة، أضف سجل تغيير الحالة
            if ($transaction->status !== 'pending') {
                $actionMap = [
                    'completed' => 'completed',
                    'failed' => 'failed',
                    'cancelled' => 'cancelled',
                ];
                
                $descriptionMap = [
                    'completed' => 'تم اكتمال المعاملة',
                    'failed' => 'فشلت المعاملة',
                    'cancelled' => 'تم إلغاء المعاملة',
                ];
                
                // إضافة سجل تغيير الحالة
                TransactionLog::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'action' => $actionMap[$transaction->status] ?? 'status_updated',
                    'description' => $descriptionMap[$transaction->status] ?? 'تم تحديث حالة المعاملة',
                    'metadata' => [
                        'old_status' => 'pending',
                        'new_status' => $transaction->status,
                    ]
                ]);
                
                // إذا كانت مكتملة، أضف التاريخ منذ ساعة واحدة
                if ($transaction->status === 'completed') {
                    $completedLog = TransactionLog::where('transaction_id', $transaction->id)
                        ->where('action', 'completed')
                        ->first();
                    
                    if ($completedLog) {
                        $completedLog->created_at = $transaction->updated_at;
                        $completedLog->save();
                    }
                }
            }
        }
    }
}
