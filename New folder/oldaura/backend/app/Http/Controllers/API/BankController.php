<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    /**
     * Obtener todos los bancos disponibles.
     */
    public function getBanks()
    {
        $banks = Bank::where('is_active', true)
                    ->select('id', 'name', 'name_ar', 'code', 'logo')
                    ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Bancos obtenidos correctamente',
            'data' => $banks
        ]);
    }

    /**
     * Obtener cuentas bancarias del usuario.
     */
    public function getUserBankAccounts(Request $request)
    {
        $userId = $request->user()->id;
        
        $accounts = BankAccount::with('bank:id,name,name_ar,logo')
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cuentas bancarias obtenidas correctamente',
            'data' => $accounts
        ]);
    }

    /**
     * Agregar una nueva cuenta bancaria para el usuario.
     */
    public function addBankAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'account_number' => 'required|string|min:5|max:30',
            'account_name' => 'required|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $userId = $request->user()->id;
        
        // Verificar si ya existe esta cuenta
        $exists = BankAccount::where('user_id', $userId)
                    ->where('bank_id', $request->bank_id)
                    ->where('account_number', $request->account_number)
                    ->exists();
        
        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta cuenta bancaria ya está registrada'
            ], 422);
        }
        
        // Si es la primera cuenta del usuario, marcarla como primaria
        $isPrimary = BankAccount::where('user_id', $userId)->count() === 0;
        
        $bankAccount = BankAccount::create([
            'user_id' => $userId,
            'bank_id' => $request->bank_id,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_primary' => $isPrimary,
            'status' => 'active'
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cuenta bancaria agregada correctamente',
            'data' => $bankAccount->load('bank:id,name,name_ar,logo')
        ]);
    }

    /**
     * Establecer una cuenta bancaria como primaria.
     */
    public function setAsPrimaryAccount(Request $request, $accountId)
    {
        $userId = $request->user()->id;
        
        $account = BankAccount::where('id', $accountId)
                    ->where('user_id', $userId)
                    ->first();
        
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cuenta bancaria no encontrada'
            ], 404);
        }
        
        // Establecer todas las cuentas como no primarias
        BankAccount::where('user_id', $userId)
                ->update(['is_primary' => false]);
        
        // Establecer esta cuenta como primaria
        $account->is_primary = true;
        $account->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cuenta establecida como primaria correctamente',
            'data' => $account->load('bank:id,name,name_ar,logo')
        ]);
    }

    /**
     * Eliminar una cuenta bancaria.
     */
    public function deleteBankAccount(Request $request, $accountId)
    {
        $userId = $request->user()->id;
        
        $account = BankAccount::where('id', $accountId)
                    ->where('user_id', $userId)
                    ->first();
        
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cuenta bancaria no encontrada'
            ], 404);
        }
        
        // Verificar si hay transacciones pendientes
        $pendingTransactions = BankTransaction::where('bank_account_id', $accountId)
                                ->where('status', 'pending')
                                ->exists();
        
        if ($pendingTransactions) {
            return response()->json([
                'status' => 'error',
                'message' => 'No es posible eliminar una cuenta con transacciones pendientes'
            ], 422);
        }
        
        // Si es la cuenta primaria, establecer otra como primaria
        if ($account->is_primary) {
            $newPrimary = BankAccount::where('user_id', $userId)
                            ->where('id', '!=', $accountId)
                            ->first();
            
            if ($newPrimary) {
                $newPrimary->is_primary = true;
                $newPrimary->save();
            }
        }
        
        $account->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cuenta bancaria eliminada correctamente'
        ]);
    }

    /**
     * Iniciar una transacción de depósito desde banco a billetera.
     */
    public function initiateDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $userId = $request->user()->id;
        
        // Verificar que la cuenta pertenezca al usuario
        $bankAccount = BankAccount::where('id', $request->bank_account_id)
                        ->where('user_id', $userId)
                        ->with('bank')
                        ->first();
        
        if (!$bankAccount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cuenta bancaria no encontrada'
            ], 404);
        }
        
        // Obtener la billetera del usuario
        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró una billetera para este usuario'
            ], 404);
        }
        
        // Crear la transacción
        $transaction = BankTransaction::create([
            'user_id' => $userId,
            'bank_id' => $bankAccount->bank_id,
            'bank_account_id' => $bankAccount->id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'transaction_type' => 'deposit',
            'status' => 'pending',
            'reference_id' => 'DEP-' . strtoupper(Str::random(10)),
            'description' => 'Depósito desde cuenta bancaria a billetera Aura',
            'transaction_date' => now(),
        ]);
        
        // Si requiere verificación OTP, generar código
        if ($transaction->requiresOtpVerification()) {
            $otpCode = $transaction->generateVerificationCode();
            
            // Aquí se implementaría el envío del OTP al usuario
            // Por ejemplo: $this->sendOtp($request->user()->phone, $otpCode);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Se ha enviado un código de verificación a su teléfono',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference_id' => $transaction->reference_id,
                    'requires_otp' => true
                ]
            ]);
        }
        
        // Si no requiere OTP, procesar directamente
        // Aquí se implementaría la lógica de integración con el banco
        
        return response()->json([
            'status' => 'success',
            'message' => 'Solicitud de depósito iniciada correctamente',
            'data' => [
                'transaction_id' => $transaction->id,
                'reference_id' => $transaction->reference_id,
                'requires_otp' => false
            ]
        ]);
    }

    /**
     * Verificar una transacción con código OTP.
     */
    public function verifyTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:bank_transactions,id',
            'verification_code' => 'required|string|size:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $userId = $request->user()->id;
        
        // Buscar la transacción
        $transaction = BankTransaction::where('id', $request->transaction_id)
                        ->where('user_id', $userId)
                        ->where('status', 'pending')
                        ->first();
        
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transacción no encontrada o ya procesada'
            ], 404);
        }
        
        // Verificar el código OTP
        $isVerified = $transaction->verifyWithCode($request->verification_code);
        
        if (!$isVerified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Código de verificación incorrecto',
                'data' => [
                    'attempts_left' => 3 - $transaction->verification_attempts
                ]
            ], 422);
        }
        
        // Transacción verificada, procesar el depósito
        // Aquí se implementaría la lógica de integración con el banco
        
        // Actualizar billetera (simulación)
        $wallet = Wallet::find($transaction->wallet_id);
        $wallet->balance += $transaction->amount;
        $wallet->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Transacción verificada y procesada correctamente',
            'data' => [
                'transaction_reference' => $transaction->reference_id,
                'wallet_balance' => $wallet->balance
            ]
        ]);
    }
}
