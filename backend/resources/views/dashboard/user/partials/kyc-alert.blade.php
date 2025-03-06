@if(auth()->user()->kyc_status !== 'approved')
<div class="alert {{ auth()->user()->kyc_status === 'rejected' ? 'alert-danger' : 'alert-warning' }} kyc-alert mb-4">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3">
            @if(auth()->user()->kyc_status === 'pending')
                <i class="bi bi-hourglass-split fs-2"></i>
            @elseif(auth()->user()->kyc_status === 'rejected')
                <i class="bi bi-exclamation-triangle fs-2"></i>
            @else
                <i class="bi bi-shield-exclamation fs-2"></i>
            @endif
        </div>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-1">
                @if(auth()->user()->kyc_status === 'pending')
                    التحقق من الهوية قيد المراجعة
                @elseif(auth()->user()->kyc_status === 'rejected')
                    تم رفض التحقق من الهوية
                @else
                    مطلوب التحقق من الهوية
                @endif
            </h5>
            @if(auth()->user()->kyc_status === 'pending')
                <p class="mb-0">طلب التحقق الخاص بك قيد المراجعة حاليًا. سيتم إخطارك بمجرد اكتمال المراجعة.</p>
            @elseif(auth()->user()->kyc_status === 'rejected')
                <p class="mb-0">تم رفض طلب التحقق الخاص بك. 
                    @if(auth()->user()->kyc_rejection_reason)
                        <strong>سبب الرفض:</strong> {{ auth()->user()->kyc_rejection_reason }}
                    @endif
                </p>
            @else
                <p class="mb-0">يجب عليك إكمال عملية التحقق من الهوية للوصول إلى جميع ميزات المنصة.</p>
            @endif
        </div>
        <div class="flex-shrink-0 ms-3">
            <a href="{{ route('user.kyc') }}" class="btn {{ auth()->user()->kyc_status === 'rejected' ? 'btn-danger' : 'btn-warning' }} btn-sm">
                @if(auth()->user()->kyc_status === 'pending')
                    عرض الحالة
                @elseif(auth()->user()->kyc_status === 'rejected')
                    إعادة التقديم
                @else
                    التحقق الآن
                @endif
            </a>
        </div>
    </div>
</div>
@endif
