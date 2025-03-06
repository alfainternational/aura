<div class="modal fade" id="kycRequiredModal" tabindex="-1" aria-labelledby="kycRequiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kycRequiredModalLabel">
                    @if(auth()->user()->kyc_status === 'pending')
                        التحقق من الهوية قيد المراجعة
                    @elseif(auth()->user()->kyc_status === 'rejected')
                        تم رفض التحقق من الهوية
                    @else
                        التحقق من الهوية مطلوب
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    @if(auth()->user()->kyc_status === 'pending')
                        <i class="bi bi-hourglass-split text-info fs-1"></i>
                    @elseif(auth()->user()->kyc_status === 'rejected')
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    @else
                        <i class="bi bi-shield-exclamation text-warning fs-1"></i>
                    @endif
                </div>
                
                @if(auth()->user()->kyc_status === 'pending')
                    <h5>طلبك قيد المراجعة</h5>
                    <p class="text-muted">طلب التحقق الخاص بك قيد المراجعة حاليًا. سيتم إخطارك بمجرد اكتمال المراجعة.</p>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle-fill me-2"></i> لا يمكنك الوصول إلى هذه الميزة حتى تتم الموافقة على طلب التحقق الخاص بك.
                    </div>
                @elseif(auth()->user()->kyc_status === 'rejected')
                    <h5>تم رفض طلبك</h5>
                    <p class="text-muted">تم رفض طلب التحقق الخاص بك. يرجى مراجعة سبب الرفض وإعادة التقديم.</p>
                    <div class="alert alert-danger mt-3">
                        <i class="bi bi-x-circle-fill me-2"></i> 
                        @if(auth()->user()->kyc_rejection_reason)
                            <strong>سبب الرفض:</strong> {{ auth()->user()->kyc_rejection_reason }}
                        @else
                            تم رفض طلبك. يرجى إعادة التقديم مع التأكد من صحة جميع المعلومات والمستندات.
                        @endif
                    </div>
                @else
                    <h5>يجب إكمال التحقق من الهوية</h5>
                    <p class="text-muted">للوصول إلى هذه الميزة، يجب عليك إكمال عملية التحقق من الهوية (KYC) أولاً.</p>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-lightbulb-fill me-2"></i> التحقق من الهوية يساعدنا على ضمان أمان وموثوقية منصتنا لجميع المستخدمين.
                    </div>
                @endif
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{ route('user.kyc') }}" class="btn btn-primary">
                    @if(auth()->user()->kyc_status === 'pending')
                        عرض حالة التحقق
                    @elseif(auth()->user()->kyc_status === 'rejected')
                        إعادة التقديم
                    @else
                        بدء التحقق
                    @endif
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
    // استدعاء هذه الدالة عند محاولة الوصول إلى ميزة تتطلب التحقق من الهوية
    function showKycRequiredModal() {
        var kycModal = new bootstrap.Modal(document.getElementById('kycRequiredModal'));
        kycModal.show();
    }
</script>
