<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Country;
use App\Models\City;

class AuthController extends Controller
{
    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm($userType = null)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $defaultCountry = Country::where('is_active', true)
                                 ->where('code', 'SD')
                                 ->first() ?? $countries->first();

        return view('auth.login', compact('userType', 'countries', 'defaultCountry'));
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
                'country_id' => 'required|exists:countries,id',
            ]);

            $remember = $request->has('remember');
            
            // تحديد ما إذا كان الدخول باستخدام البريد الإلكتروني أو اسم المستخدم أو رقم الهاتف
            $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) 
                ? 'email' 
                : (is_numeric($request->login) ? 'phone_number' : 'username');
            
            // محاولة تسجيل الدخول
            if (Auth::attempt([
                $loginField => $request->login, 
                'password' => $request->password,
                'country_id' => $request->country_id,
                'is_active' => true
            ], $remember)) {
                $user = Auth::user();
                
                // تحديث وقت آخر تسجيل دخول
                $user->update([
                    'last_login_at' => now(),
                    'last_ip' => $request->ip()
                ]);
                
                // تسجيل عملية تسجيل الدخول الناجحة
                Log::info('تسجيل دخول ناجح', [
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                    'login_field' => $loginField,
                    'country_id' => $request->country_id
                ]);
                
                // التحقق من تفعيل المصادقة الثنائية
                if ($user->two_factor_enabled) {
                    // تسجيل خروج المستخدم مؤقتًا
                    Auth::logout();
                    
                    // تخزين معلومات المستخدم في الجلسة للتحقق لاحقًا
                    $request->session()->put('auth.two_factor.required', true);
                    $request->session()->put('auth.two_factor.user_id', $user->id);
                    $request->session()->put('auth.two_factor.remember', $remember);
                    
                    // توجيه المستخدم إلى صفحة التحقق بخطوتين
                    return redirect()->route('two-factor.form');
                }

                // التحقق من تفعيل المصادقة البيومترية
                if ($user->biometric_enabled && !$request->has('biometric_auth')) {
                    $request->session()->put('biometric_auth_available', true);
                }
                
                // إعادة توجيه المستخدم إلى الصفحة المناسبة حسب نوعه
                return redirect($this->redirectTo())
                    ->with('success', 'تم تسجيل الدخول بنجاح');
            }
            
            // تسجيل محاولة تسجيل دخول فاشلة
            Log::warning('محاولة تسجيل دخول فاشلة', [
                'login' => $request->login,
                'login_field' => $loginField,
                'ip' => $request->ip(),
                'country_id' => $request->country_id
            ]);
            
            // إذا فشل تسجيل الدخول
            return redirect()->back()
                ->withInput($request->only('login', 'remember', 'country_id'))
                ->with('error', 'بيانات تسجيل الدخول غير صحيحة');
                
        } catch (\Exception $e) {
            Log::error('خطأ في تسجيل الدخول: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput($request->only('login', 'remember', 'country_id'))
                ->with('error', 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض صفحة تسجيل مستخدم جديد
     */
    public function showRegistrationForm()
    {
        $countries = Country::getRegistrationAllowedCountries();
        $defaultCountry = Country::where('code', 'SD')->first();
        
        if ($countries->isEmpty()) {
            return redirect()->route('login')
                ->with('error', 'التسجيل غير متاح حالياً، يرجى المحاولة لاحقاً');
        }
        
        return view('auth.register', compact('countries', 'defaultCountry'));
    }

    /**
     * الحصول على مدن دولة معينة
     */
    public function getCitiesByCountry(Request $request)
    {
        $countryId = $request->input('country_id');
        $cities = City::where('country_id', $countryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return response()->json($cities);
    }

    /**
     * معالجة عملية تسجيل مستخدم جديد
     */
    public function register(Request $request)
    {
        try {
            // التحقق من أن الدولة تسمح بالتسجيل
            $country = Country::find($request->country_id);
            if (!$country || !$country->isRegistrationAllowed()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'التسجيل غير متاح في هذه الدولة حالياً');
            }
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:50|unique:users,username|regex:/^[\w\d\sأ-يًَُِّْ-]+$/u',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone_number' => 'required|string|max:20|unique:users,phone_number',
                'gender' => 'required|in:male,female',
                'birth_date' => 'nullable|date',
                'user_type' => 'required|in:customer,merchant,agent,messenger',
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'terms_accepted' => 'required|accepted',
            ], [
                'username.unique' => 'اسم المستخدم مستخدم بالفعل',
                'username.regex' => 'اسم المستخدم يجب أن يحتوي على أحرف عربية أو إنجليزية أو أرقام فقط',
                'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
                'phone_number.unique' => 'رقم الهاتف مستخدم بالفعل',
                'terms_accepted.accepted' => 'يجب الموافقة على شروط الاستخدام',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'فشل التسجيل. يرجى التحقق من البيانات المدخلة.');
            }

            // إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'is_active' => true,
                'is_verified' => false,
                'requires_kyc' => true,
                'verification_code' => mt_rand(100000, 999999),
                'last_ip' => $request->ip(),
            ]);

            // إنشاء ملف التعريف حسب نوع المستخدم
            switch ($request->user_type) {
                case 'customer':
                    $this->createCustomerProfile($user, $request);
                    break;
                case 'merchant':
                    $this->createMerchantProfile($user, $request);
                    break;
                case 'agent':
                    $this->createAgentProfile($user, $request);
                    break;
                case 'messenger':
                    $this->createMessengerProfile($user, $request);
                    break;
            }

            // إرسال رمز التحقق (تنفيذ ذلك لاحقًا)
            // $this->sendVerificationCode($user);

            // تسجيل دخول المستخدم بعد التسجيل
            Auth::login($user);

            // توجيه المستخدم إلى صفحة التحقق
            $routeName = $user->user_type . '.verification';
            return redirect()->route($routeName)
                ->with('success', 'تم التسجيل بنجاح. يرجى استكمال عملية التحقق من حسابك.');

        } catch (\Exception $e) {
            Log::error('فشل تسجيل المستخدم: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض صفحة نسيت كلمة المرور
     */
    public function showForgotPasswordForm()
    {
        $countries = Country::where('is_active', true)->get();
        $defaultCountry = Country::where('code', 'SD')->first();
        
        return view('auth.forgot-password', compact('countries', 'defaultCountry'));
    }

    /**
     * معالجة طلب إعادة تعيين كلمة المرور
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'country_id' => 'required|exists:countries,id',
        ]);

        $user = User::where('email', $request->email)
            ->where('country_id', $request->country_id)
            ->first();
            
        if (!$user) {
            return back()->with('error', 'لم نتمكن من العثور على مستخدم بهذا البريد الإلكتروني في هذه الدولة');
        }
        
        // إنشاء رمز إعادة تعيين كلمة المرور
        $resetCode = mt_rand(100000, 999999);
        $user->update([
            'reset_code' => $resetCode,
            'reset_code_expires_at' => now()->addHours(1)
        ]);
        
        // إرسال رمز إعادة التعيين (تنفيذ ذلك لاحقًا)
        // $this->sendResetCode($user, $resetCode);
        
        // تخزين البريد الإلكتروني في الجلسة للتحقق لاحقًا
        $request->session()->put('password_reset_email', $request->email);
        $request->session()->put('password_reset_country_id', $request->country_id);
        
        return redirect()->route('password.reset.code')
            ->with('success', 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
    }

    /**
     * عرض صفحة التحقق من البريد الإلكتروني
     */
    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    /**
     * إرسال بريد التحقق مرة أخرى
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended();
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'تم إرسال رابط التحقق!');
    }

    /**
     * عرض صفحة التحقق بخطوتين
     */
    public function showTwoFactorForm()
    {
        return view('auth.two-factor');
    }

    /**
     * التحقق من رمز التحقق بخطوتين
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // هنا يتم التحقق من الرمز المقدم (يحتاج إلى مكتبة خارجية مثل Google Authenticator)
        // هذه مجرد محاكاة بسيطة للعملية
        if ($request->code === '123456') { // في الإنتاج سيتم استبدال هذا بتحقق حقيقي
            return redirect()->intended();
        }

        return back()->withErrors(['code' => 'الرمز غير صحيح. حاول مرة أخرى.']);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * توجيه المستخدم بناءً على نوعه
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    protected function redirectBasedOnUserType($user)
    {
        // التحقق من اكتمال بيانات KYC
        if ($user->requires_kyc && !$user->is_verified) {
            Session::flash('warning', 'يرجى استكمال بيانات التحقق الخاصة بك للوصول إلى جميع الخدمات');
        }
        
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->user_type === 'customer') {
            // التحقق من اكتمال ملف التعريف للعميل
            if (!$user->customerProfile) {
                // إنشاء ملف تعريف فارغ إذا لم يكن موجوداً
                $user->customerProfile()->create([
                    'user_id' => $user->id,
                    'is_guest' => false,
                    'referral_code' => Str::random(8),
                ]);
            }
            return redirect()->route('customer.dashboard');
        } elseif ($user->user_type === 'merchant') {
            // التحقق من اكتمال ملف التعريف
            if (!$user->merchantProfile) {
                return redirect()->route('merchant.complete-profile');
            }
            return redirect()->route('merchant.dashboard');
        } elseif ($user->user_type === 'agent') {
            // التحقق من اكتمال ملف التعريف
            if (!$user->agentProfile) {
                return redirect()->route('agent.complete-profile');
            }
            return redirect()->route('agent.dashboard');
        } elseif ($user->user_type === 'messenger') {
            // التحقق من اكتمال ملف التعريف
            if (!$user->messengerProfile) {
                return redirect()->route('messenger.complete-profile');
            }
            return redirect()->route('messenger.dashboard');
        } else {
            // نوع مستخدم غير معروف
            return redirect()->route('home');
        }
    }

    /**
     * توجيه المستخدم لاستكمال ملفه الشخصي
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    protected function redirectToCompleteProfile($user)
    {
        if ($user->user_type === 'merchant') {
            return redirect()->route('merchant.complete-profile');
        } elseif ($user->user_type === 'agent') {
            return redirect()->route('agent.complete-profile');
        } elseif ($user->user_type === 'messenger') {
            return redirect()->route('messenger.complete-profile');
        } elseif ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            // العملاء يتم توجيههم مباشرة للوحة التحكم
            return redirect()->route('customer.dashboard');
        }
    }

    private function createCustomerProfile($user, $request)
    {
        // إنشاء ملف تعريف للعميل
        $user->customerProfile()->create([
            'user_id' => $user->id,
            'is_guest' => false,
            'referral_code' => Str::random(8),
        ]);
    }

    private function createMerchantProfile($user, $request)
    {
        // إنشاء ملف تعريف للتاجر
        $user->merchantProfile()->create([
            'user_id' => $user->id,
            // إضافة بيانات التاجر الأخرى
        ]);
    }

    private function createAgentProfile($user, $request)
    {
        // إنشاء ملف تعريف للوكيل
        $user->agentProfile()->create([
            'user_id' => $user->id,
            // إضافة بيانات الوكيل الأخرى
        ]);
    }

    private function createMessengerProfile($user, $request)
    {
        // إنشاء ملف تعريف للرسول
        $user->messengerProfile()->create([
            'user_id' => $user->id,
            // إضافة بيانات الرسول الأخرى
        ]);
    }

    private function redirectTo()
    {
        if (auth()->check()) {
            return \App\Providers\RouteServiceProvider::getHomeRoute(auth()->user());
        }
        return '/login';
    }
}
