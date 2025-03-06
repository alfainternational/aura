<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthRoleMiddleware - موحد لجميع ميدلوير التحقق من الأدوار
 * 
 * هذا الميدلوير يجمع وظائف جميع ميدلوير الأدوار السابقة في مكان واحد:
 * - CheckRole
 * - Role
 * - RoleChecker
 * - CheckUserRole
 * - CheckUserType
 * - CheckAllUserType
 * 
 * يمكن استخدامه بعدة طرق:
 * 1. التحقق من نوع المستخدم (user_type): 'role:admin' أو 'checkrole:admin'
 * 2. التحقق من عدة أنواع: 'role:admin,supervisor' أو 'checkrole:admin,supervisor'
 * 3. التحقق من KYC: سيتم التحقق تلقائياً إذا كان المستخدم يتطلب KYC
 */
class AuthRoleMiddleware
{
    /**
     * قائمة المسارات المسموحة حتى بدون KYC للمستخدمين الذين يتطلبون التحقق
     */
    protected $allowedRoutesWithoutKYC = [
        'complete-profile',
        'dashboard',
        'verification',
        'profile',
        'settings',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // تسجيل تنفيذ الميدلوير للتصحيح
            Log::debug('AuthRoleMiddleware executed', [
                'user_id' => $request->user() ? $request->user()->id : 'Guest',
                'required_roles' => $roles
            ]);

            // التحقق من تسجيل الدخول
            if (!Auth::check()) {
                Log::warning('User not authenticated, redirecting to login');
                return redirect('login');
            }

            // الحصول على نوع المستخدم
            $user = $request->user();
            $userType = $user->user_type;
            
            // تسجيل عملية التحقق
            Log::info('Checking user roles', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'required_roles' => $roles
            ]);
            
            // إصلاح مصفوفة الأدوار إذا كانت مصفوفة متداخلة
            if (isset($roles[0]) && is_array($roles[0])) {
                $roles = $roles[0];
            }
            
            // إذا كانت معلمة الأدوار تحتوي على قيم مفصولة بفواصل، قم بتقسيمها
            if (count($roles) === 1 && strpos($roles[0], ',') !== false) {
                $roles = explode(',', $roles[0]);
            }
            
            // التحقق مما إذا كان المستخدم لديه أي من الأدوار المسموح بها
            if (in_array($userType, $roles)) {
                Log::info('User has required role', [
                    'user_id' => $user->id,
                    'user_type' => $userType,
                    'matched_role' => $userType
                ]);
                
                // التحقق من حالة KYC إذا كان المستخدم يتطلب التحقق
                if ($this->shouldCheckKyc($user)) {
                    $currentRoute = $request->route()->getName();
                    
                    // التحقق مما إذا كان المسار الحالي مسموحًا به بدون KYC
                    $isAllowedWithoutKYC = $this->isRouteAllowedWithoutKYC($currentRoute, $userType);
                    
                    if (!$isAllowedWithoutKYC) {
                        Log::info('KYC verification check: Verification not complete', [
                            'user_id' => $user->id,
                            'user_type' => $user->user_type,
                            'is_verified' => $user->is_verified
                        ]);
                        
                        // التوجيه إلى صفحة استكمال بيانات KYC
                        return redirect()->route($userType . '.verification')
                            ->with('warning', 'يجب عليك استكمال عملية التحقق للوصول إلى هذه الميزة.');
                    }
                }
                
                // إذا اجتاز جميع الفحوصات، استمر في معالجة الطلب
                return $next($request);
            }
            
            // تسجيل فشل التحقق
            Log::warning('User type does not match any allowed roles', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'required_roles' => $roles
            ]);
            
            // التوجيه حسب نوع المستخدم
            return $this->redirectBasedOnUserType($userType);
        } catch (\Exception $e) {
            Log::error('Error in AuthRoleMiddleware', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('home')
                ->with('error', 'حدث خطأ أثناء التحقق من صلاحياتك. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * التحقق مما إذا كان يجب التحقق من KYC للمستخدم
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    protected function shouldCheckKyc($user)
    {
        return $user->requires_kyc && !$user->is_verified;
    }

    /**
     * التحقق مما إذا كان المسار مسموحًا به بدون KYC
     * 
     * @param string $routeName
     * @param string $userType
     * @return bool
     */
    protected function isRouteAllowedWithoutKYC($routeName, $userType)
    {
        // إذا كان المسار فارغًا، السماح بالوصول
        if (empty($routeName)) {
            return true;
        }
        
        // التحقق من كل مسار مسموح به
        foreach ($this->allowedRoutesWithoutKYC as $allowedRoute) {
            $fullRouteName = $userType . '.' . $allowedRoute;
            
            // التحقق من المطابقة المباشرة أو إذا كان المسار يبدأ بالمسار المسموح به
            if ($routeName === $fullRouteName || strpos($routeName, $fullRouteName . '.') === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * توجيه المستخدم حسب نوعه
     * 
     * @param string $userType
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnUserType($userType)
    {
        // تحديد مسارات التوجيه لكل نوع مستخدم
        $homeRoutes = [
            'admin' => '/admin/dashboard',
            'customer' => '/customer/dashboard',
            'merchant' => '/merchant/dashboard',
            'agent' => '/agent/dashboard',
            'messenger' => '/messenger/dashboard',
        ];

        // التحقق مما إذا كان RouteServiceProvider يحتوي على مسارات رئيسية محددة
        if (property_exists(RouteServiceProvider::class, 'HOME_ROUTES')) {
            $homeRoutes = array_merge($homeRoutes, RouteServiceProvider::$HOME_ROUTES);
        }

        // تحديد مسار التوجيه بناءً على نوع المستخدم
        if (isset($homeRoutes[$userType])) {
            Log::info('Redirecting user to their dashboard', [
                'user_type' => $userType,
                'redirect_path' => $homeRoutes[$userType]
            ]);
            return redirect($homeRoutes[$userType])->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        // إذا لم يكن هناك مسار محدد، التوجيه إلى الصفحة الرئيسية
        Log::info('No specific redirect path for user type, redirecting to home', [
            'user_type' => $userType
        ]);
        return redirect(RouteServiceProvider::HOME)->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
    }
}
