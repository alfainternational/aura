<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BiometricSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BiometricController extends Controller
{
    /**
     * عرض صفحة تسجيل البصمة
     */
    public function showRegisterForm()
    {
        return view('auth.biometric-register');
    }
    
    /**
     * بدء عملية تسجيل البصمة
     */
    public function startRegistration(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }
        
        // إنشاء تحدي عشوائي للتسجيل
        $challenge = base64_encode(random_bytes(32));
        $request->session()->put('biometric_challenge', $challenge);
        
        // إنشاء معرف المستخدم
        $userId = 'user-' . $user->id;
        
        // إعداد بيانات التسجيل
        $registrationOptions = [
            'challenge' => $challenge,
            'rp' => [
                'name' => 'منصة أورا',
                'id' => $request->getHost()
            ],
            'user' => [
                'id' => $userId,
                'name' => $user->username,
                'displayName' => $user->name
            ],
            'pubKeyCredParams' => [
                [
                    'type' => 'public-key',
                    'alg' => -7 // ES256
                ],
                [
                    'type' => 'public-key',
                    'alg' => -257 // RS256
                ]
            ],
            'timeout' => 60000,
            'attestation' => 'none',
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'userVerification' => 'preferred',
                'requireResidentKey' => false
            ]
        ];
        
        return response()->json([
            'status' => 'success',
            'options' => $registrationOptions
        ]);
    }
    
    /**
     * إكمال عملية تسجيل البصمة
     */
    public function completeRegistration(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }
        
        // التحقق من التحدي
        $challenge = $request->session()->get('biometric_challenge');
        if (!$challenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'انتهت صلاحية التحدي، يرجى المحاولة مرة أخرى'
            ], 400);
        }
        
        // التحقق من البيانات المستلمة
        $credential = $request->input('credential');
        if (!$credential || !isset($credential['id']) || !isset($credential['response'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صالحة'
            ], 400);
        }
        
        // حفظ بيانات البصمة
        try {
            $deviceInfo = [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'registered_at' => now()->toIso8601String()
            ];
            
            // إنشاء جلسة بصمة جديدة
            $biometricSession = $user->addBiometricSession(
                Str::uuid()->toString(),
                $request->input('device_name', 'جهاز غير معروف'),
                $credential['id'],
                json_encode($credential['response']),
                $deviceInfo
            );
            
            // تحديث حالة المستخدم
            $user->update([
                'biometric_enabled' => true,
                'biometric_registered_at' => now()
            ]);
            
            // مسح التحدي من الجلسة
            $request->session()->forget('biometric_challenge');
            
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل البصمة بنجاح',
                'session_id' => $biometricSession->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('فشل تسجيل البصمة: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل البصمة'
            ], 500);
        }
    }
    
    /**
     * بدء عملية تسجيل الدخول بالبصمة
     */
    public function startAuthentication(Request $request)
    {
        // إنشاء تحدي عشوائي للتسجيل
        $challenge = base64_encode(random_bytes(32));
        $request->session()->put('biometric_auth_challenge', $challenge);
        
        // الحصول على معرف المستخدم إذا تم تقديمه
        $username = $request->input('username');
        $allowCredentials = [];
        
        if ($username) {
            $user = User::where('username', $username)
                ->orWhere('email', $username)
                ->first();
            
            if ($user && $user->biometric_enabled) {
                // الحصول على جلسات البصمة النشطة
                $sessions = $user->getActiveBiometricSessions();
                
                foreach ($sessions as $session) {
                    $allowCredentials[] = [
                        'type' => 'public-key',
                        'id' => $session->credential_id
                    ];
                }
                
                $request->session()->put('biometric_auth_user_id', $user->id);
            }
        }
        
        // إعداد خيارات المصادقة
        $authOptions = [
            'challenge' => $challenge,
            'timeout' => 60000,
            'rpId' => $request->getHost(),
            'userVerification' => 'preferred'
        ];
        
        if (!empty($allowCredentials)) {
            $authOptions['allowCredentials'] = $allowCredentials;
        }
        
        return response()->json([
            'status' => 'success',
            'options' => $authOptions
        ]);
    }
    
    /**
     * إكمال عملية تسجيل الدخول بالبصمة
     */
    public function completeAuthentication(Request $request)
    {
        // التحقق من التحدي
        $challenge = $request->session()->get('biometric_auth_challenge');
        if (!$challenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'انتهت صلاحية التحدي، يرجى المحاولة مرة أخرى'
            ], 400);
        }
        
        // التحقق من البيانات المستلمة
        $credential = $request->input('credential');
        if (!$credential || !isset($credential['id']) || !isset($credential['response'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صالحة'
            ], 400);
        }
        
        try {
            // البحث عن جلسة البصمة المطابقة
            $session = BiometricSession::where('credential_id', $credential['id'])
                ->where('is_active', true)
                ->first();
            
            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على بيانات البصمة'
                ], 404);
            }
            
            $user = $session->user;
            
            if (!$user || !$user->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم غير نشط أو غير موجود'
                ], 403);
            }
            
            // تحديث آخر استخدام للبصمة
            $session->updateLastUsed();
            
            // تسجيل دخول المستخدم
            Auth::login($user);
            
            // تحديث وقت آخر تسجيل دخول
            $user->update(['last_login_at' => now()]);
            
            // مسح التحدي من الجلسة
            $request->session()->forget('biometric_auth_challenge');
            $request->session()->forget('biometric_auth_user_id');
            
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح',
                'redirect' => route('dashboard')
            ]);
            
        } catch (\Exception $e) {
            Log::error('فشل تسجيل الدخول بالبصمة: ' . $e->getMessage(), [
                'credential_id' => $credential['id'],
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الدخول بالبصمة'
            ], 500);
        }
    }
    
    /**
     * إدارة جلسات البصمة
     */
    public function manageSessions()
    {
        $user = Auth::user();
        $sessions = $user->getActiveBiometricSessions();
        
        return view('auth.biometric-sessions', compact('sessions'));
    }
    
    /**
     * حذف جلسة بصمة
     */
    public function deleteSession(Request $request, $id)
    {
        $user = Auth::user();
        $session = BiometricSession::find($id);
        
        if (!$session || $session->user_id !== $user->id) {
            return redirect()->back()->with('error', 'لم يتم العثور على الجلسة');
        }
        
        $session->deactivate();
        
        // إذا لم يعد هناك جلسات نشطة، تعطيل البصمة
        if ($user->getActiveBiometricSessions()->isEmpty()) {
            $user->update(['biometric_enabled' => false]);
        }
        
        return redirect()->back()->with('success', 'تم حذف جلسة البصمة بنجاح');
    }
}
