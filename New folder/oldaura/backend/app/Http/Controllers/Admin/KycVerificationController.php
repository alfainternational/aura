<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\KycStatusUpdate;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KycVerificationController extends Controller
{
    /**
     * خدمة الإشعارات
     * Servicio de notificaciones
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * إنشاء مثيل جديد من المتحكم
     * Constructor del controlador
     *
     * @param \App\Services\NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('checkrole:admin');
        $this->notificationService = $notificationService;
    }

    /**
     * عرض قائمة طلبات التحقق من الهوية
     * Muestra la lista de solicitudes de verificación KYC con filtros y estadísticas
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = KycVerification::with('user');
        
        // تصفية حسب الحالة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // تصفية حسب النوع المستند
        if ($request->has('document_type') && $request->document_type) {
            $query->where('document_type', $request->document_type);
        }
        
        // تصفية حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // بحث حسب الاسم أو البريد الإلكتروني
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }
        
        // ترتيب حسب التاريخ (الأحدث أولاً) أو أي حقل آخر
        if ($request->has('sort') && $request->sort) {
            $direction = $request->has('direction') ? $request->direction : 'desc';
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $verifications = $query->paginate($request->input('per_page', 15));
        
        // إحصائيات للوحة التحكم
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $lastWeek = [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()];
        
        $total = KycVerification::count();
        $pending = KycVerification::where('status', 'pending')->count();
        $approved = KycVerification::where('status', 'approved')->count();
        $rejected = KycVerification::where('status', 'rejected')->count();
        
        $stats = [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'new_today' => KycVerification::whereDate('created_at', $today)->count(),
            'new_yesterday' => KycVerification::whereDate('created_at', $yesterday)->count(),
            'new_this_week' => KycVerification::whereBetween('created_at', $thisWeek)->count(),
            'new_last_week' => KycVerification::whereBetween('created_at', $lastWeek)->count(),
            'pending_percent' => $total > 0 ? round(($pending / $total) * 100) : 0,
            'approved_percent' => $total > 0 ? round(($approved / $total) * 100) : 0,
            'rejected_percent' => $total > 0 ? round(($rejected / $total) * 100) : 0,
            'avg_approval_time' => $this->calculateAverageApprovalTime(),
        ];
        
        // الحصول على آخر الطلبات المعتمدة والمرفوضة
        $latestApproved = KycVerification::with('user')
            ->where('status', 'approved')
            ->orderBy('verified_at', 'desc')
            ->limit(5)
            ->get();
            
        $latestRejected = KycVerification::with('user')
            ->where('status', 'rejected')
            ->orderBy('verified_at', 'desc')
            ->limit(5)
            ->get();
            
        // أنواع المستندات المتاحة للتصفية
        $documentTypes = KycVerification::select('document_type')
            ->distinct()
            ->pluck('document_type');
        
        return view('admin.kyc.index', compact(
            'verifications', 
            'stats', 
            'latestApproved', 
            'latestRejected',
            'documentTypes'
        ));
    }
    
    /**
     * عرض تفاصيل طلب التحقق من الهوية
     * Muestra los detalles de una solicitud de verificación KYC específica
     * 
     * @param int $id ID de la verificación
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $verification = KycVerification::with(['user' => function($query) {
            $query->with('kycVerifications');
        }])->findOrFail($id);
        
        // تحديث حالة "مشاهدة" إذا كان الطلب في حالة انتظار
        if ($verification->status === 'pending' && !$verification->viewed_at) {
            $verification->viewed_at = now();
            $verification->save();
        }
        
        // جلب تاريخ التحقق السابق للمستخدم
        $previousVerifications = $verification->user->kycVerifications()
            ->where('id', '!=', $verification->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // الإحصائيات والملاحظات
        $notes = $verification->notes()->orderBy('created_at', 'desc')->get();
        
        return view('admin.kyc.show', compact('verification', 'previousVerifications', 'notes'));
    }
    
    /**
     * عرض صفحة مراجعة طلب التحقق من الهوية
     */
    public function review($id)
    {
        $verification = KycVerification::with('user')->findOrFail($id);
        
        // التحقق من أن الطلب في حالة انتظار
        if ($verification->status !== 'pending') {
            return redirect()->route('admin.kyc.show', $id)
                ->with('error', 'لا يمكن مراجعة طلب تم اتخاذ قرار بشأنه بالفعل');
        }
        
        return view('admin.kyc.review', compact('verification'));
    }
    
    /**
     * تحديث حالة طلب التحقق من الهوية
     */
    public function updateStatus(Request $request, $id)
    {
        $verification = KycVerification::with('user')->findOrFail($id);
        
        // التحقق من البيانات المدخلة
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected',
            'other_reason' => 'required_if:rejection_reason,أخرى',
            'admin_notes' => 'nullable|string|max:500',
        ]);
        
        // تحديث حالة الطلب
        $verification->status = $request->status;
        $verification->verified_at = now();
        $verification->verified_by = auth()->id();
        
        // إضافة سبب الرفض إذا تم رفض الطلب
        if ($request->status === 'rejected') {
            $rejectionReason = $request->rejection_reason;
            
            // إذا كان السبب "أخرى"، استخدم النص المدخل
            if ($rejectionReason === 'أخرى' && $request->has('other_reason')) {
                $rejectionReason = $request->other_reason;
            }
            
            $verification->rejection_reason = $rejectionReason;
        } else {
            $verification->rejection_reason = null;
        }
        
        // إضافة ملاحظات إدارية
        if ($request->has('admin_notes')) {
            $verification->admin_notes = $request->admin_notes;
        }
        
        // حفظ التغييرات
        $verification->save();
        
        // تحديث حالة التحقق من الهوية للمستخدم
        $user = $verification->user;
        $user->kyc_verified = ($request->status === 'approved');
        $user->save();
        
        // إرسال إشعار للمستخدم
        $this->notificationService->sendKycStatusUpdated($user, $verification);
        
        return redirect()->route('admin.kyc.index')
            ->with('success', 'تم تحديث حالة طلب التحقق من الهوية بنجاح');
    }
    
    /**
     * عرض لوحة معلومات التحقق من الهوية
     */
    public function dashboard()
    {
        // إحصائيات عامة
        $totalVerifications = KycVerification::count();
        $pendingVerifications = KycVerification::where('status', 'pending')->count();
        $approvedVerifications = KycVerification::where('status', 'approved')->count();
        $rejectedVerifications = KycVerification::where('status', 'rejected')->count();
        
        $stats = [
            'total' => $totalVerifications,
            'pending' => $pendingVerifications,
            'approved' => $approvedVerifications,
            'rejected' => $rejectedVerifications,
            'pending_percent' => $totalVerifications > 0 ? round(($pendingVerifications / $totalVerifications) * 100) : 0,
            'approved_percent' => $totalVerifications > 0 ? round(($approvedVerifications / $totalVerifications) * 100) : 0,
            'rejected_percent' => $totalVerifications > 0 ? round(($rejectedVerifications / $totalVerifications) * 100) : 0,
            'new_today' => KycVerification::whereDate('created_at', Carbon::today())->count(),
            'completed_today' => KycVerification::whereDate('verified_at', Carbon::today())->count(),
        ];
        
        // إحصائيات شهرية
        $monthlyStats = $this->getMonthlyStats();
        
        // آخر الطلبات
        $latestVerifications = KycVerification::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // طلبات قيد المراجعة
        $pendingVerificationsList = KycVerification::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();
        
        return view('admin.kyc.dashboard', compact(
            'stats', 
            'monthlyStats', 
            'latestVerifications', 
            'pendingVerificationsList'
        ));
    }
    
    /**
     * الحصول على إحصائيات شهرية للرسم البياني
     */
    private function getMonthlyStats()
    {
        // تاريخ اليوم
        $today = Carbon::today();
        
        // الحصول على بيانات آخر 6 أشهر
        $labels = [];
        $pendingData = [];
        $approvedData = [];
        $rejectedData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $labels[] = $month->translatedFormat('F');
            
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            // عدد الطلبات حسب الحالة للشهر
            $pendingData[] = KycVerification::where('status', 'pending')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
                
            $approvedData[] = KycVerification::where('status', 'approved')
                ->whereBetween('verified_at', [$startOfMonth, $endOfMonth])
                ->count();
                
            $rejectedData[] = KycVerification::where('status', 'rejected')
                ->whereBetween('verified_at', [$startOfMonth, $endOfMonth])
                ->count();
        }
        
        return [
            'labels' => $labels,
            'pending' => $pendingData,
            'approved' => $approvedData,
            'rejected' => $rejectedData,
        ];
    }
    
    /**
     * حساب متوسط وقت الموافقة على طلبات التحقق
     * Calcula el tiempo promedio de aprobación de las solicitudes KYC
     * 
     * @return float|null
     */
    protected function calculateAverageApprovalTime()
    {
        $approvedVerifications = KycVerification::where('status', 'approved')
            ->whereNotNull('verified_at')
            ->whereNotNull('created_at')
            ->get();
            
        if ($approvedVerifications->isEmpty()) {
            return null;
        }
        
        $totalHours = 0;
        foreach ($approvedVerifications as $verification) {
            $created = new Carbon($verification->created_at);
            $verified = new Carbon($verification->verified_at);
            $totalHours += $created->diffInHours($verified);
        }
        
        return round($totalHours / $approvedVerifications->count(), 1);
    }
    
    /**
     * عرض مستند التحقق من الهوية
     */
    public function viewDocument($id, $type)
    {
        $verification = KycVerification::findOrFail($id);
        
        $allowedTypes = ['id_front', 'id_back', 'selfie', 'additional_document'];
        
        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }
        
        $field = $type . '_path';
        
        if (!$verification->$field) {
            abort(404);
        }
        
        // التحقق من وجود الملف
        if (!Storage::disk('private')->exists($verification->$field)) {
            abort(404);
        }
        
        return response()->file(
            Storage::disk('private')->path($verification->$field)
        );
    }
    
    /**
     * تنزيل جميع مستندات التحقق كملف مضغوط
     */
    public function downloadAllDocuments($id)
    {
        $verification = KycVerification::with('user')->findOrFail($id);
        $user = $verification->user;
        
        $zipFileName = "kyc_documents_{$user->id}.zip";
        $zipFilePath = storage_path("app/temp/{$zipFileName}");
        
        // إنشاء مجلد مؤقت إذا لم يكن موجودًا
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }
        
        // إنشاء ملف مضغوط
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            // إضافة المستندات إلى الملف المضغوط
            $documents = [
                'id_front_path' => 'ID Front',
                'id_back_path' => 'ID Back',
                'selfie_path' => 'Selfie',
                'additional_document_path' => 'Additional Document'
            ];
            
            foreach ($documents as $field => $label) {
                if ($verification->$field && Storage::disk('private')->exists($verification->$field)) {
                    $originalPath = Storage::disk('private')->path($verification->$field);
                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                    $zip->addFile($originalPath, "{$label}.{$extension}");
                }
            }
            
            $zip->close();
            
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'حدث خطأ أثناء إنشاء الملف المضغوط');
    }
}
