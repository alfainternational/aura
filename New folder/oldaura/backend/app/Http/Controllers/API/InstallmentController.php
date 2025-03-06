<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Models\Installment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstallmentController extends Controller
{
    /**
     * Obtener opciones de pago a plazos para una orden.
     */
    public function getInstallmentOptions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $order = Order::findOrFail($request->order_id);
        $amount = $order->total_amount;
        
        // Crear opciones de cuotas: 4, 6, 8, 12 meses
        $options = [
            ['months' => 4, 'interest' => 0],
            ['months' => 6, 'interest' => 0],
            ['months' => 8, 'interest' => 0],
            ['months' => 12, 'interest' => 0.05]  // 5% de interés para 12 meses
        ];
        
        $installmentOptions = [];
        
        foreach ($options as $option) {
            $calculation = InstallmentPlan::calculateTotalWithInterest($amount, $option['months']);
            
            $installmentOptions[] = [
                'months' => $option['months'],
                'total_amount' => round($calculation['total_amount'], 2),
                'installment_amount' => round($calculation['installment_amount'], 2),
                'interest_rate' => $calculation['interest_rate'] * 100,  // Convertir a porcentaje
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Opciones de pago a plazos obtenidas correctamente',
            'data' => [
                'order_id' => $order->id,
                'order_amount' => $amount,
                'installment_options' => $installmentOptions
            ]
        ]);
    }

    /**
     * Crear un plan de pago a plazos para una orden.
     */
    public function createInstallmentPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'number_of_installments' => 'required|in:4,6,8,12',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $order = Order::findOrFail($request->order_id);
        $userId = $request->user()->id;
        $merchantId = $order->merchant_id;
        
        // Verificar que la orden no tenga ya un plan de pagos
        $existingPlan = InstallmentPlan::where('order_id', $order->id)->exists();
        
        if ($existingPlan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta orden ya tiene un plan de pagos activo'
            ], 422);
        }
        
        // Verificar que la orden sea del usuario y no esté pagada
        if ($order->user_id !== $userId || $order->payment_status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'No se puede crear un plan de pagos para esta orden'
            ], 422);
        }
        
        // Calcular los montos con interés
        $calculation = InstallmentPlan::calculateTotalWithInterest(
            $order->total_amount, 
            $request->number_of_installments
        );
        
        // Crear el plan de pagos con transacción
        try {
            DB::beginTransaction();
            
            $startDate = Carbon::now();
            $endDate = (clone $startDate)->addMonths($request->number_of_installments - 1);
            
            $installmentPlan = InstallmentPlan::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'merchant_id' => $merchantId,
                'total_amount' => $calculation['total_amount'],
                'installment_amount' => $calculation['installment_amount'],
                'number_of_installments' => $request->number_of_installments,
                'frequency' => 'monthly',
                'interest_rate' => $calculation['interest_rate'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
            ]);
            
            // Generar las cuotas individuales
            $installmentPlan->generateInstallments();
            
            // Actualizar la orden
            $order->payment_method = 'installment';
            $order->payment_status = 'partial';
            $order->save();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Plan de pagos creado correctamente',
                'data' => [
                    'plan_id' => $installmentPlan->id,
                    'total_amount' => $installmentPlan->total_amount,
                    'installment_amount' => $installmentPlan->installment_amount,
                    'number_of_installments' => $installmentPlan->number_of_installments,
                    'start_date' => $installmentPlan->start_date->format('Y-m-d'),
                    'end_date' => $installmentPlan->end_date->format('Y-m-d'),
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el plan de pagos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener los planes de pago activos del usuario.
     */
    public function getUserInstallmentPlans(Request $request)
    {
        $userId = $request->user()->id;
        
        $plans = InstallmentPlan::with(['installments', 'order'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($plan) {
                    $paid = $plan->installments->where('status', 'paid')->count();
                    $total = $plan->number_of_installments;
                    
                    return [
                        'id' => $plan->id,
                        'order_id' => $plan->order_id,
                        'merchant_name' => $plan->order->merchant_name ?? 'Desconocido',
                        'total_amount' => $plan->total_amount,
                        'installment_amount' => $plan->installment_amount,
                        'progress' => "{$paid}/{$total}",
                        'progress_percentage' => ($paid / $total) * 100,
                        'status' => $plan->status,
                        'start_date' => $plan->start_date->format('Y-m-d'),
                        'next_due_date' => $plan->installments->where('status', '!=', 'paid')
                                            ->sortBy('due_date')
                                            ->first()?->due_date->format('Y-m-d'),
                    ];
                });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Planes de pago obtenidos correctamente',
            'data' => $plans
        ]);
    }

    /**
     * Obtener detalles de un plan de pago específico.
     */
    public function getInstallmentPlanDetails(Request $request, $planId)
    {
        $userId = $request->user()->id;
        
        $plan = InstallmentPlan::with(['installments' => function ($query) {
                    $query->orderBy('installment_number', 'asc');
                }, 'order', 'merchant:id,name,username'])
                ->where('id', $planId)
                ->where('user_id', $userId)
                ->first();
        
        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan de pagos no encontrado'
            ], 404);
        }
        
        $installments = $plan->installments->map(function ($installment) {
            return [
                'id' => $installment->id,
                'installment_number' => $installment->installment_number,
                'amount' => $installment->amount,
                'status' => $installment->status,
                'due_date' => $installment->due_date->format('Y-m-d'),
                'paid_date' => $installment->paid_date ? $installment->paid_date->format('Y-m-d') : null,
            ];
        });
        
        $planDetails = [
            'id' => $plan->id,
            'order_id' => $plan->order_id,
            'merchant_name' => $plan->merchant->name ?? 'Desconocido',
            'total_amount' => $plan->total_amount,
            'installment_amount' => $plan->installment_amount,
            'number_of_installments' => $plan->number_of_installments,
            'interest_rate' => $plan->interest_rate * 100, // Convertir a porcentaje
            'start_date' => $plan->start_date->format('Y-m-d'),
            'end_date' => $plan->end_date->format('Y-m-d'),
            'status' => $plan->status,
            'installments' => $installments,
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Detalles del plan de pagos obtenidos correctamente',
            'data' => $planDetails
        ]);
    }

    /**
     * Pagar una cuota específica.
     */
    public function payInstallment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'installment_id' => 'required|exists:installments,id',
            'payment_method' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $userId = $request->user()->id;
        
        // Buscar la cuota y verificar que pertenezca al usuario
        $installment = Installment::with('installmentPlan')
                        ->whereHas('installmentPlan', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        })
                        ->where('id', $request->installment_id)
                        ->first();
        
        if (!$installment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cuota no encontrada o sin acceso'
            ], 404);
        }
        
        // Verificar que la cuota no esté pagada
        if ($installment->status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta cuota ya ha sido pagada'
            ], 422);
        }
        
        // Procesar el pago (aquí iría la integración con pasarela de pago)
        // ...
        
        // Simulación de pago exitoso
        try {
            DB::beginTransaction();
            
            // Marcar la cuota como pagada
            $installment->markAsPaid(
                $request->payment_method,
                'PAY-' . time()
            );
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Cuota pagada correctamente',
                'data' => [
                    'installment_id' => $installment->id,
                    'installment_number' => $installment->installment_number,
                    'payment_reference' => $installment->payment_reference,
                    'plan_status' => $installment->installmentPlan->status,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}
