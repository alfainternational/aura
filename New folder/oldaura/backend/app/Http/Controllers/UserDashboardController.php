<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewKycSubmission;

class UserDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrole:user');
    }

    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        // Aquí podrías cargar datos adicionales para el dashboard
        
        return view('dashboard.user.index', compact('user'));
    }

    /**
     * Show the user statistics page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function statistics()
    {
        $user = Auth::user();
        // Cargar estadísticas del usuario
        
        return view('dashboard.user.statistics', compact('user'));
    }

    /**
     * Show the user notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        $user = Auth::user();
        // Cargar notificaciones del usuario
        $notifications = []; // Esto sería reemplazado por notificaciones reales
        
        return view('dashboard.user.notifications', compact('user', 'notifications'));
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllNotificationsAsRead()
    {
        // Lógica para marcar todas las notificaciones como leídas
        
        return redirect()->route('user.notifications')->with('success', 'تم تعيين جميع الإشعارات كمقروءة');
    }

    /**
     * Show the user profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('dashboard.user.profile', compact('user'));
    }

    /**
     * Update the user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->bio = $request->bio;
        $user->save();
        
        return redirect()->route('user.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Update the user contact information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateContactInfo(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'country' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->country = $request->country;
        $user->city = $request->city;
        $user->address = $request->address;
        $user->save();
        
        return redirect()->route('user.profile')->with('success', 'تم تحديث معلومات الاتصال بنجاح');
    }

    /**
     * Update the user preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:ar,en',
            'timezone' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->language = $request->language;
        $user->timezone = $request->timezone;
        $user->save();
        
        return redirect()->route('user.profile')->with('success', 'تم تحديث التفضيلات بنجاح');
    }

    /**
     * Update the user avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Eliminar avatar anterior si existe
        if ($user->avatar && $user->avatar !== 'avatar-placeholder.jpg') {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }
        
        // Guardar nuevo avatar
        $avatarName = time() . '.' . $request->avatar->extension();
        $request->avatar->storeAs('avatars', $avatarName, 'public');
        
        $user->avatar = $avatarName;
        $user->save();
        
        return redirect()->route('user.profile')->with('success', 'تم تحديث الصورة الشخصية بنجاح');
    }

    /**
     * Update the user status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:online,away,busy,offline',
            'status_message' => 'nullable|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->status = $request->status;
        $user->status_message = $request->status_message;
        $user->save();
        
        return redirect()->route('user.profile')->with('success', 'تم تحديث الحالة بنجاح');
    }

    /**
     * Show the user navigation settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function navigation()
    {
        $user = Auth::user();
        
        return view('dashboard.user.navigation', compact('user'));
    }

    /**
     * Show the user settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function settings()
    {
        $user = Auth::user();
        
        return view('dashboard.user.settings', compact('user'));
    }

    /**
     * Update the general settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneralSettings(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'default_view' => 'required|string|in:grid,list',
            'enable_animations' => 'nullable|boolean',
            'auto_refresh' => 'nullable|boolean',
            'refresh_interval' => 'required|integer|min:30|max:600',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.settings')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Aquí guardarías las preferencias en la base de datos
        // Por ejemplo, podrías usar una tabla user_settings o un campo JSON en la tabla users
        
        return redirect()->route('user.settings')->with('success', 'تم تحديث الإعدادات العامة بنجاح');
    }

    /**
     * Update the notification settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotificationSettings(Request $request)
    {
        $user = Auth::user();
        
        // Validar y guardar configuraciones de notificaciones
        
        return redirect()->route('user.settings')->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }

    /**
     * Update the appearance settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAppearanceSettings(Request $request)
    {
        $user = Auth::user();
        
        // Validar y guardar configuraciones de apariencia
        
        return redirect()->route('user.settings')->with('success', 'تم تحديث إعدادات المظهر بنجاح');
    }

    /**
     * Update the language settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLanguageSettings(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:ar,en',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string|in:12h,24h',
            'first_day_of_week' => 'required|string|in:sunday,monday,saturday',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.settings')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Guardar configuraciones de idioma y región
        
        return redirect()->route('user.settings')->with('success', 'تم تحديث إعدادات اللغة والمنطقة بنجاح');
    }

    /**
     * Deactivate the user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateAccount(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'deactivation_reason' => 'nullable|string',
            'deactivation_feedback' => 'nullable|string|max:1000',
            'confirm_deactivation' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.settings')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para desactivar la cuenta
        $user->is_active = false;
        $user->deactivated_at = now();
        $user->deactivation_reason = $request->deactivation_reason;
        $user->save();
        
        Auth::logout();
        
        return redirect()->route('login')->with('info', 'تم تعطيل حسابك بنجاح. يمكنك تفعيله مرة أخرى عن طريق تسجيل الدخول.');
    }

    /**
     * Delete the user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'deletion_reason' => 'nullable|string',
            'deletion_feedback' => 'nullable|string|max:1000',
            'password' => 'required|string',
            'confirm_deletion' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.settings')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('user.settings')
                ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                ->withInput();
        }
        
        // Guardar feedback antes de eliminar
        // Aquí podrías guardar el feedback en una tabla separada
        
        // Eliminar cuenta
        $user->delete();
        
        Auth::logout();
        
        return redirect()->route('home')->with('info', 'تم حذف حسابك بنجاح. نأسف لرؤيتك تغادر.');
    }

    /**
     * Show the security settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function security()
    {
        $user = Auth::user();
        
        // الحصول على سجل تسجيل الدخول (يمكن تنفيذ هذا لاحقًا)
        $loginHistory = [];
        
        // الحصول على رموز الاسترداد إذا كانت المصادقة الثنائية مفعلة
        $recoveryCodes = [];
        if ($user->two_factor_enabled && $user->two_factor_recovery_codes) {
            $recoveryCodes = json_decode($user->two_factor_recovery_codes);
        }
        
        return view('dashboard.user.security', compact('user', 'loginHistory', 'recoveryCodes'));
    }

    /**
     * Change the user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('security.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        // التحقق من صحة كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('security.index')
                ->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة'])
                ->withInput();
        }
        
        // تحديث كلمة المرور
        $user->password = Hash::make($request->password);
        $user->save();
        
        // تسجيل نشاط تغيير كلمة المرور
        $this->logSecurityActivity($user, 'تغيير كلمة المرور', 'تم تغيير كلمة المرور بنجاح');
        
        return redirect()->route('security.index')->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Show the connected devices page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function connectedDevices()
    {
        $user = Auth::user();
        
        // الحصول على الجهاز الحالي
        $currentDevice = $user->connectedDevices()->current()->first();
        
        // الحصول على الأجهزة الأخرى
        $otherDevices = $user->connectedDevices()->where('is_current_device', false)->get();
        
        return view('dashboard.user.connected-devices', compact('user', 'currentDevice', 'otherDevices'));
    }

    /**
     * Toggle the trust status of a device.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleDeviceTrust($id)
    {
        $user = Auth::user();
        $device = $user->connectedDevices()->findOrFail($id);
        
        // تبديل حالة الثقة
        $device->toggleTrust();
        
        // تسجيل النشاط
        $action = $device->is_trusted ? 'تعيين جهاز موثوق' : 'إلغاء الثقة بجهاز';
        $this->logSecurityActivity($user, $action, 'تم ' . $action . ': ' . $device->device_name);
        
        return redirect()->route('security.devices')->with('success', 'تم تحديث حالة الجهاز بنجاح');
    }

    /**
     * Logout from a specific device.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutDevice($id)
    {
        $user = Auth::user();
        $device = $user->connectedDevices()->findOrFail($id);
        
        // حذف الجهاز
        $device->delete();
        
        // تسجيل النشاط
        $this->logSecurityActivity($user, 'تسجيل خروج من جهاز', 'تم تسجيل الخروج من: ' . $device->device_name);
        
        return redirect()->route('security.devices')->with('success', 'تم تسجيل الخروج من الجهاز بنجاح');
    }

    /**
     * Logout from all devices except the current one.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutAllDevices()
    {
        $user = Auth::user();
        
        // حذف جميع الأجهزة باستثناء الجهاز الحالي
        $user->connectedDevices()->where('is_current_device', false)->delete();
        
        // تسجيل النشاط
        $this->logSecurityActivity($user, 'تسجيل خروج من جميع الأجهزة', 'تم تسجيل الخروج من جميع الأجهزة الأخرى');
        
        return redirect()->route('security.devices')->with('success', 'تم تسجيل الخروج من جميع الأجهزة الأخرى بنجاح');
    }

    /**
     * Log a security activity.
     *
     * @param  \App\Models\User  $user
     * @param  string  $action
     * @param  string  $description
     * @return void
     */
    private function logSecurityActivity($user, $action, $description)
    {
        // يمكن تنفيذ هذا لاحقًا لتسجيل أنشطة الأمان
        // مثال: $user->securityLogs()->create(['action' => $action, 'description' => $description]);
    }

    /**
     * Show the KYC verification page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function kyc()
    {
        $user = Auth::user();
        
        // Obtener la última verificación KYC del usuario
        $latestKyc = $user->kycVerifications()->latest()->first();
        
        // Determinar el estado de verificación KYC
        if ($latestKyc) {
            $kycStatus = $latestKyc->status; // pending, approved, rejected
            $kycRejectionReason = $latestKyc->rejection_reason;
            $kycSubmittedAt = $latestKyc->submitted_at;
            $kycVerifiedAt = $latestKyc->verified_at;
        } else {
            $kycStatus = 'not_submitted';
            $kycRejectionReason = null;
            $kycSubmittedAt = null;
            $kycVerifiedAt = null;
        }
        
        return view('dashboard.user.kyc', compact(
            'user', 
            'kycStatus', 
            'kycRejectionReason', 
            'kycSubmittedAt', 
            'kycVerifiedAt'
        ));
    }

    /**
     * Submit KYC verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitKyc(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:2',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'id_type' => 'required|string|in:national_id,passport,residence',
            'id_number' => 'required|string|max:50',
            'id_expiry' => 'required|date|after:today',
            'id_front' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'id_back' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'selfie' => 'required|file|mimes:jpeg,png,jpg|max:5120',
            'confirm_accuracy' => 'required|accepted',
            'terms_agreement' => 'required|accepted',
            'data_processing' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('user.kyc')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Guardar archivos en almacenamiento privado
            $idFrontPath = $request->file('id_front')->store('kyc_documents/' . $user->id, 'private');
            $idBackPath = $request->file('id_back')->store('kyc_documents/' . $user->id, 'private');
            $selfiePath = $request->file('selfie')->store('kyc_documents/' . $user->id, 'private');
            
            // Crear o actualizar la verificación KYC
            $kycVerification = \App\Models\KycVerification::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'pending'],
                [
                    'full_name' => $request->full_name,
                    'date_of_birth' => $request->date_of_birth,
                    'nationality' => $request->nationality,
                    'address' => $request->address,
                    'city' => $request->city,
                    'country' => $request->country,
                    'phone_number' => $request->phone_number,
                    'id_type' => $request->id_type,
                    'id_number' => $request->id_number,
                    'id_front_path' => $idFrontPath,
                    'id_back_path' => $idBackPath,
                    'selfie_path' => $selfiePath,
                    'status' => 'pending',
                    'submitted_at' => now(),
                ]
            );
            
            // Actualizar el estado de verificación del usuario
            $user->kyc_status = 'pending';
            $user->kyc_submitted_at = now();
            $user->kyc_step = 4; // Completó todos los pasos
            $user->save();
            
            // Enviar notificación al administrador
            try {
                $adminUsers = \App\Models\User::where('user_type', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\NewKycSubmission($user));
                }
                
                // Notificar al usuario que su solicitud ha sido recibida
                $user->notify(new \App\Notifications\KycSubmissionReceived());
            } catch (\Exception $e) {
                // Registrar el error pero continuar con el proceso
                \Log::error('Error al enviar notificaciones de KYC: ' . $e->getMessage());
            }
            
            return redirect()->route('user.kyc')->with('success', 'تم تقديم طلب التحقق بنجاح. سيتم مراجعته في أقرب وقت ممكن.');
        } catch (\Exception $e) {
            return redirect()->route('user.kyc')
                ->with('error', 'حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى لاحقًا.')
                ->withInput();
        }
    }

    /**
     * Export user data.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportData()
    {
        $user = Auth::user();
        
        // Generar archivo de exportación de datos
        // Esto requeriría una implementación real
        
        return response()->download('path/to/exported/data.zip')->deleteFileAfterSend(true);
    }
}
