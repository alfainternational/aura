<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MerchantProfile;
use App\Models\AgentProfile;
use App\Models\MessengerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    /**
     * Constructor del controlador
     * Aplica los middlewares necesarios para la administración
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrole:admin');
    }

    /**
     * عرض لوحة تحكم المدير
     * Muestra el panel principal de administración con estadísticas y datos generales
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        // إحصائيات مختلفة للعرض في لوحة التحكم
        $stats = [
            'users_count' => User::where('user_type', 'customer')->count(),
            'merchants_count' => User::where('user_type', 'merchant')->count(),
            'agents_count' => User::where('user_type', 'agent')->count(),
            'messengers_count' => User::where('user_type', 'messenger')->count(),
            'supervisors_count' => User::where('user_type', 'admin')
                ->where('role', 'supervisor')->count(),
            'admins_count' => User::where('user_type', 'admin')
                ->whereIn('role', ['admin', 'super_admin'])->count(),
            'registered_today' => User::whereDate('created_at', now()->toDateString())->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        // آخر المستخدمين المسجلين
        $latestUsers = User::latest()->take(10)->get();

        // آخر التجار المسجلين
        $latestMerchants = User::where('user_type', 'merchant')
            ->latest()
            ->take(5)
            ->get();
            
        // رسم بياني للمستخدمين المسجلين خلال الأسبوع الماضي
        $last7Days = collect(range(0, 6))->map(function ($days) {
            $date = now()->subDays($days)->format('Y-m-d');
            return [
                'date' => $date,
                'count' => User::whereDate('created_at', $date)->count()
            ];
        })->reverse()->values();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestMerchants', 'last7Days'));
    }

    /**
     * عرض قائمة المستخدمين
     * Muestra la lista de usuarios con opciones de filtrado y búsqueda
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function users(Request $request)
    {
        $query = User::query();
        
        // تصفية حسب نوع المستخدم
        if ($request->has('type') && $request->type) {
            $query->where('user_type', $request->type);
        }
        
        // تصفية حسب الحالة
        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status == 'active');
        }
        
        // بحث حسب الاسم أو البريد الإلكتروني أو رقم الهاتف
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        
        // ترتيب النتائج
        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->latest();
        }
        
        $users = $query->paginate($request->input('per_page', 20));
        
        // إحصائيات موجزة للمستخدمين
        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'customers' => User::where('user_type', 'customer')->count(),
            'merchants' => User::where('user_type', 'merchant')->count(),
            'agents' => User::where('user_type', 'agent')->count(),
            'messengers' => User::where('user_type', 'messenger')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'userStats'));
    }

    /**
     * عرض تفاصيل مستخدم
     * Muestra los detalles de un usuario específico
     * 
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showUser($id)
    {
        $user = User::with(['kycVerifications', 'loginHistories' => function($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }])->findOrFail($id);
        
        // جلب البيانات المرتبطة بناءً على نوع المستخدم
        switch ($user->user_type) {
            case 'merchant':
                $profile = $user->merchantProfile;
                break;
            case 'agent':
                $profile = $user->agentProfile;
                break;
            case 'messenger':
                $profile = $user->messengerProfile;
                break;
            default:
                $profile = null;
        }
        
        // إحصائيات إضافية للمستخدم
        $stats = [
            'days_since_registration' => now()->diffInDays($user->created_at),
            'login_count' => $user->loginHistories()->count(),
            'last_login' => $user->loginHistories()->latest()->first()?->created_at,
            'verification_status' => $user->kycVerifications()->latest()->first()?->status ?? 'not_submitted',
        ];
        
        return view('admin.users.show', compact('user', 'profile', 'stats'));
    }

    /**
     * تعطيل/تفعيل حساب مستخدم
     * Activa o desactiva una cuenta de usuario
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        
        // التحقق من صلاحيات المشرف لتعديل هذا المستخدم
        if ($user->user_type === 'admin' && !Gate::allows('manage-admins')) {
            return redirect()->route('admin.users')->with('error', 'ليس لديك صلاحية لتعديل حساب مدير');
        }
        
        // تبديل حالة المستخدم
        $user->is_active = !$user->is_active;
        $user->save();
        
        // تسجيل النشاط
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log($user->is_active ? 'تفعيل حساب مستخدم' : 'تعطيل حساب مستخدم');
        
        $statusMessage = $user->is_active ? 'تم تفعيل' : 'تم تعطيل';
        return redirect()->back()->with('success', "{$statusMessage} حساب المستخدم بنجاح");
    }

    /**
     * عرض صفحة إدارة المشرفين
     */
    public function supervisors()
    {
        $supervisors = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->paginate(20);
            
        return view('admin.supervisors.index', compact('supervisors'));
    }

    /**
     * عرض نموذج إضافة مشرف جديد
     */
    public function createSupervisor()
    {
        return view('admin.supervisors.create');
    }

    /**
     * حفظ مشرف جديد
     */
    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20',
        ]);
        
        DB::transaction(function() use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'admin',
                'role' => 'supervisor',
                'phone_number' => $request->phone_number,
                'permissions' => json_encode([
                    'view_dashboard', 'manage_messengers', 'view_messenger_reports', 
                    'assign_deliveries', 'track_messengers'
                ]),
                'email_verified_at' => now(),
            ]);
        });
        
        return redirect()->route('admin.supervisors')
            ->with('success', 'تم إضافة المشرف بنجاح');
    }

    /**
     * عرض صفحة تعديل مشرف
     */
    public function editSupervisor($id)
    {
        $supervisor = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->findOrFail($id);
            
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    /**
     * تحديث بيانات مشرف
     */
    public function updateSupervisor(Request $request, $id)
    {
        $supervisor = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->findOrFail($id);
            
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $supervisor->name = $request->name;
        $supervisor->email = $request->email;
        $supervisor->phone_number = $request->phone_number;
        
        if ($request->filled('password')) {
            $supervisor->password = Hash::make($request->password);
        }
        
        $supervisor->save();
        
        return redirect()->route('admin.supervisors')
            ->with('success', 'تم تحديث بيانات المشرف بنجاح');
    }

    /**
     * حذف مشرف
     */
    public function deleteSupervisor($id)
    {
        $supervisor = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->findOrFail($id);
            
        // تغيير المناديب المرتبطين بهذا المشرف
        MessengerProfile::where('supervisor_id', $supervisor->id)
            ->update(['supervisor_id' => null]);
            
        $supervisor->delete();
        
        return redirect()->route('admin.supervisors')
            ->with('success', 'تم حذف المشرف بنجاح');
    }

    /**
     * عرض صفحة إدارة المناديب
     */
    public function messengers()
    {
        $messengers = User::where('user_type', 'messenger')
            ->with('messengerProfile')
            ->paginate(20);
            
        return view('admin.messengers.index', compact('messengers'));
    }

    /**
     * عرض نموذج إضافة مندوب جديد
     */
    public function createMessenger()
    {
        $supervisors = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->get();
            
        return view('admin.messengers.create', compact('supervisors'));
    }

    /**
     * حفظ مندوب جديد
     */
    public function storeMessenger(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20',
            'zone' => 'required|string|max:100',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_plate' => 'required|string|max:20',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);
        
        DB::transaction(function() use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'messenger',
                'phone_number' => $request->phone_number,
                'email_verified_at' => now(),
            ]);
            
            // إنشاء ملف تعريف المندوب
            MessengerProfile::create([
                'user_id' => $user->id,
                'messenger_id' => 'MSG' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'zone' => $request->zone,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate' => $request->vehicle_plate,
                'supervisor_id' => $request->supervisor_id,
                'is_verified' => true,
            ]);
        });
        
        return redirect()->route('admin.messengers')
            ->with('success', 'تم إضافة المندوب بنجاح');
    }

    /**
     * عرض صفحة تعديل مندوب
     */
    public function editMessenger($id)
    {
        $messenger = User::where('user_type', 'messenger')
            ->with('messengerProfile')
            ->findOrFail($id);
            
        $supervisors = User::where('user_type', 'admin')
            ->where('role', 'supervisor')
            ->get();
            
        return view('admin.messengers.edit', compact('messenger', 'supervisors'));
    }

    /**
     * تحديث بيانات مندوب
     */
    public function updateMessenger(Request $request, $id)
    {
        $messenger = User::where('user_type', 'messenger')
            ->with('messengerProfile')
            ->findOrFail($id);
            
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'zone' => 'required|string|max:100',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_plate' => 'required|string|max:20',
            'supervisor_id' => 'nullable|exists:users,id',
            'is_verified' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $messenger->name = $request->name;
        $messenger->email = $request->email;
        $messenger->phone_number = $request->phone_number;
        
        if ($request->filled('password')) {
            $messenger->password = Hash::make($request->password);
        }
        
        $messenger->save();
        
        // تحديث ملف تعريف المندوب
        $profile = $messenger->messengerProfile;
        $profile->zone = $request->zone;
        $profile->vehicle_type = $request->vehicle_type;
        $profile->vehicle_plate = $request->vehicle_plate;
        $profile->supervisor_id = $request->supervisor_id;
        $profile->is_verified = $request->is_verified ?? $profile->is_verified;
        $profile->save();
        
        return redirect()->route('admin.messengers')
            ->with('success', 'تم تحديث بيانات المندوب بنجاح');
    }

    /**
     * حذف مندوب
     */
    public function deleteMessenger($id)
    {
        $messenger = User::where('user_type', 'messenger')
            ->findOrFail($id);
            
        $messenger->delete();
        
        return redirect()->route('admin.messengers')
            ->with('success', 'تم حذف المندوب بنجاح');
    }
}
