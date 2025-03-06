@extends('layouts.admin')

@section('title', 'معالجة طلبات التحقق من الهوية بالذكاء الاصطناعي')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>معالجة طلبات التحقق من الهوية (KYC) بالذكاء الاصطناعي</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-robot text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">إجمالي الطلبات قيد المراجعة:</span> {{ $pendingVerifications->count() }}
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <a href="{{ route('admin.kyc.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة إلى القائمة
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <!-- زر معالجة الطلبات المحددة باستخدام الذكاء الاصطناعي -->
                    <div class="px-4 mb-3">
                        <form id="batchProcessForm" action="{{ route('admin.kyc.batch-process-with-ai') }}" method="POST">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="fas fa-check-square"></i> تحديد الكل
                                    </button>
                                    <button type="button" id="deselectAllBtn" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-square"></i> إلغاء التحديد
                                    </button>
                                </div>
                                <button type="submit" id="processSelectedBtn" class="btn btn-sm btn-info" disabled>
                                    <i class="fas fa-robot"></i> معالجة الطلبات المحددة باستخدام الذكاء الاصطناعي
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <div class="form-check ps-3">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المستخدم</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">نوع الهوية</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ التقديم</th>
                                    <th class="text-secondary opacity-7">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingVerifications as $verification)
                                <tr>
                                    <td>
                                        <div class="form-check ps-3">
                                            <input class="form-check-input verification-checkbox" type="checkbox" name="verification_ids[]" value="{{ $verification->id }}" form="batchProcessForm">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $verification->user->profile_photo_url ?? asset('img/default-avatar.png') }}" class="avatar avatar-sm me-3" alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $verification->user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $verification->user->email }}</p>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $verification->user->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @switch($verification->id_type)
                                                @case('national_id')
                                                    بطاقة هوية وطنية
                                                    @break
                                                @case('passport')
                                                    جواز سفر
                                                    @break
                                                @case('residence')
                                                    إقامة
                                                    @break
                                                @default
                                                    {{ $verification->id_type }}
                                            @endswitch
                                        </p>
                                        <p class="text-xs text-secondary mb-0">{{ $verification->id_number }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $verification->submitted_at ? $verification->submitted_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-sm btn-info me-2" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.kyc.process-with-ai', $verification->id) }}" class="btn btn-sm btn-primary" title="معالجة باستخدام الذكاء الاصطناعي">
                                                <i class="fas fa-robot"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا توجد طلبات تحقق من الهوية قيد المراجعة</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $pendingVerifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديد/إلغاء تحديد جميع الصناديق
        $('#selectAll').change(function() {
            $('.verification-checkbox').prop('checked', $(this).prop('checked'));
            updateProcessButton();
        });
        
        // زر تحديد الكل
        $('#selectAllBtn').click(function() {
            $('.verification-checkbox').prop('checked', true);
            $('#selectAll').prop('checked', true);
            updateProcessButton();
        });
        
        // زر إلغاء التحديد
        $('#deselectAllBtn').click(function() {
            $('.verification-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateProcessButton();
        });
        
        // تحديث حالة زر المعالجة
        $('.verification-checkbox').change(function() {
            updateProcessButton();
        });
        
        // تحديث حالة زر المعالجة
        function updateProcessButton() {
            var checkedCount = $('.verification-checkbox:checked').length;
            $('#processSelectedBtn').prop('disabled', checkedCount === 0);
            $('#processSelectedBtn').text(
                checkedCount > 0 
                ? 'معالجة ' + checkedCount + ' طلب باستخدام الذكاء الاصطناعي' 
                : 'معالجة الطلبات المحددة باستخدام الذكاء الاصطناعي'
            );
        }
    });
</script>
@endsection
