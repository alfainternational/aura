<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Generar y enviar un código OTP
     */
    public function generateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|in:transaction,login,password_reset,account_verification',
            'transaction_id' => 'required_if:purpose,transaction|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = $request->user();
        $purpose = $request->input('purpose');
        
        // Generar OTP
        $otp = $this->otpService->generateOtp($user, $purpose);
        
        // Enviar OTP al usuario
        $this->otpService->sendOtp($user, $otp, $purpose);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Código de verificación enviado correctamente',
            'data' => [
                'expiry_minutes' => 15, // Tiempo de expiración del OTP en minutos
                'purpose' => $purpose,
                // Si hay un transaction_id, lo incluimos en la respuesta
                'transaction_id' => $request->input('transaction_id')
            ]
        ]);
    }

    /**
     * Verificar un código OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|in:transaction,login,password_reset,account_verification',
            'code' => 'required|string|size:6',
            'transaction_id' => 'required_if:purpose,transaction|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = $request->user();
        $purpose = $request->input('purpose');
        $code = $request->input('code');
        
        // Verificar OTP
        $isValid = $this->otpService->verifyOtp($user, $purpose, $code);
        
        if (!$isValid) {
            // Obtener información sobre el OTP, incluyendo intentos
            $otpInfo = $this->otpService->getOtpInfo($user->id, $purpose);
            $attemptsLeft = $otpInfo ? 3 - $otpInfo['attempts'] : 0;
            
            return response()->json([
                'status' => 'error',
                'message' => 'Código de verificación incorrecto',
                'data' => [
                    'attempts_left' => max(0, $attemptsLeft)
                ]
            ], 422);
        }
        
        // Si llegamos aquí, el OTP es válido
        // Aquí iría la lógica dependiendo del propósito
        
        return response()->json([
            'status' => 'success',
            'message' => 'Código de verificación correcto',
            'data' => [
                'purpose' => $purpose,
                'transaction_id' => $request->input('transaction_id')
            ]
        ]);
    }

    /**
     * Reenviar un código OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|in:transaction,login,password_reset,account_verification',
            'transaction_id' => 'required_if:purpose,transaction|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = $request->user();
        $purpose = $request->input('purpose');
        
        // Invalidar OTP anterior
        $this->otpService->invalidateOtp($user->id, $purpose);
        
        // Generar nuevo OTP
        $otp = $this->otpService->generateOtp($user, $purpose);
        
        // Enviar OTP al usuario
        $this->otpService->sendOtp($user, $otp, $purpose);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Código de verificación reenviado correctamente',
            'data' => [
                'expiry_minutes' => 15,
                'purpose' => $purpose,
                'transaction_id' => $request->input('transaction_id')
            ]
        ]);
    }
}
