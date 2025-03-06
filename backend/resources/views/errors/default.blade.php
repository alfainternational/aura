@extends('layouts.app')

@section('title', 'خطأ في التطبيق')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-danger text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        حدث خطأ غير متوقع
                    </h3>
                </div>
                <div class="card-body text-center p-5">
                    <h2 class="mb-4 text-danger">
                        {{ $message ?? 'حدث خطأ غير متوقع في التطبيق' }}
                    </h2>
                    
                    <p class="lead text-muted mb-4">
                        نأسف للإزعاج. يبدو أن هناك مشكلة في التطبيق.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>
                            العودة للصفحة الرئيسية
                        </a>
                        <button onclick="window.location.reload()" class="btn btn-outline-secondary">
                            <i class="fas fa-sync me-2"></i>
                            إعادة تحميل الصفحة
                        </button>
                    </div>
                    
                    @if(config('app.debug'))
                        <div class="mt-5 text-start">
                            <h5 class="text-danger">تفاصيل الخطأ (للمطورين):</h5>
                            <pre class="bg-light p-3 rounded">
                                {{ 
                                    isset($exception) 
                                    ? $exception->getMessage() 
                                    : 'لا توجد تفاصيل إضافية للخطأ' 
                                }}
                            </pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.error-page {
    background-color: #f8f9fa;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush
