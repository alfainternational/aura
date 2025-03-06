<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletService
{
    /**
     * Crear una nueva transacción en la billetera
     *
     * @param User $user
     * @param float $amount
     * @param string $type
     * @param string $description
     * @param array $details
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return Transaction
     */
    public function createTransaction(
        User $user,
        float $amount,
        string $type,
        string $description = '',
        array $details = [],
        ?string $referenceId = null,
        ?string $referenceType = null
    ): Transaction {
        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => config('payment.default_currency', 'USD'),
            'type' => $type,
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'transaction_details' => $details,
        ]);
    }

    /**
     * Depositar fondos en la billetera del usuario
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param array $details
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return Transaction
     */
    public function deposit(
        User $user,
        float $amount,
        string $description = 'Depósito de fondos',
        array $details = [],
        ?string $referenceId = null,
        ?string $referenceType = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception('El monto del depósito debe ser mayor que cero.');
        }

        try {
            DB::beginTransaction();

            // Crear la transacción
            $transaction = $this->createTransaction(
                $user,
                $amount,
                'deposit',
                $description,
                $details,
                $referenceId,
                $referenceType
            );

            // Actualizar el saldo del usuario
            $user->wallet_balance = $user->wallet_balance + $amount;
            $user->save();

            // Marcar la transacción como completada
            $transaction->markAsCompleted();

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al depositar fondos en la billetera: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'details' => $details
            ]);
            throw $e;
        }
    }

    /**
     * Retirar fondos de la billetera del usuario
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param array $details
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return Transaction
     */
    public function withdraw(
        User $user,
        float $amount,
        string $description = 'Retiro de fondos',
        array $details = [],
        ?string $referenceId = null,
        ?string $referenceType = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception('El monto del retiro debe ser mayor que cero.');
        }

        if ($user->wallet_balance < $amount) {
            throw new Exception('Saldo insuficiente para realizar el retiro.');
        }

        try {
            DB::beginTransaction();

            // Crear la transacción (usamos valor negativo para retiros)
            $transaction = $this->createTransaction(
                $user,
                -$amount,
                'withdrawal',
                $description,
                $details,
                $referenceId,
                $referenceType
            );

            // Actualizar el saldo del usuario
            $user->wallet_balance = $user->wallet_balance - $amount;
            $user->save();

            // Marcar la transacción como completada
            $transaction->markAsCompleted();

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al retirar fondos de la billetera: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'details' => $details
            ]);
            throw $e;
        }
    }

    /**
     * Procesar un pago utilizando la billetera
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param array $details
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return Transaction
     */
    public function processPayment(
        User $user,
        float $amount,
        string $description = 'Pago con billetera',
        array $details = [],
        ?string $referenceId = null,
        ?string $referenceType = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception('El monto del pago debe ser mayor que cero.');
        }

        if ($user->wallet_balance < $amount) {
            throw new Exception('Saldo insuficiente para realizar el pago.');
        }

        try {
            DB::beginTransaction();

            // Crear la transacción (usamos valor negativo para pagos)
            $transaction = $this->createTransaction(
                $user,
                -$amount,
                'payment',
                $description,
                $details,
                $referenceId,
                $referenceType
            );

            // Actualizar el saldo del usuario
            $user->wallet_balance = $user->wallet_balance - $amount;
            $user->save();

            // Marcar la transacción como completada
            $transaction->markAsCompleted();

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar pago con la billetera: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'details' => $details
            ]);
            throw $e;
        }
    }

    /**
     * Procesar un reembolso a la billetera
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @param array $details
     * @param string|null $referenceId
     * @param string|null $referenceType
     * @return Transaction
     */
    public function processRefund(
        User $user,
        float $amount,
        string $description = 'Reembolso a billetera',
        array $details = [],
        ?string $referenceId = null,
        ?string $referenceType = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception('El monto del reembolso debe ser mayor que cero.');
        }

        try {
            DB::beginTransaction();

            // Crear la transacción
            $transaction = $this->createTransaction(
                $user,
                $amount,
                'refund',
                $description,
                $details,
                $referenceId,
                $referenceType
            );

            // Actualizar el saldo del usuario
            $user->wallet_balance = $user->wallet_balance + $amount;
            $user->save();

            // Marcar la transacción como completada
            $transaction->markAsCompleted();

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar reembolso a la billetera: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'details' => $details
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si el usuario tiene saldo suficiente
     *
     * @param User $user
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $user->wallet_balance >= $amount;
    }

    /**
     * Obtener el historial de transacciones de un usuario
     *
     * @param User $user
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTransactionHistory(User $user, int $perPage = 15)
    {
        return Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener el saldo de la billetera de un usuario
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        return (float) $user->wallet_balance;
    }
}
