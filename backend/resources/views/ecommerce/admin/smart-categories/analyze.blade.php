@extends('layouts.admin')

@section('title', 'تحليل الفئة - ' . $category->name)

@section('styles')
<style>
    .ai-badge {
        background: linear-gradient(45deg, #6b46c1, #805ad5);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
    }
    .attribute-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border-left: 4px solid #805ad5;
    }
    .keyword-tag {
        display: inline-block;
        background-color: #e2e8f0;
        color: #4a5568;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    .keyword-tag.new {
        background-color: #c6f6d5;
        color: #276749;
    }
    .highlight {
        background-color: #fefcbf;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">تحليل الفئة: {{ $category->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
        <li class="breadcrumb-item"><a href="{{ route('commerce.categories.index') }}">التصنيفات</a></li>
        <li class="breadcrumb-item"><a href="{{ route('commerce.smart-categories.index') }}">التصنيفات الذكية</a></li>
        <li class="breadcrumb-item active">تحليل الفئة</li>
    </ol>

    <div class="row">
        <!-- معلومات الفئة -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    معلومات الفئة
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $category->name }}</h5>
                    <p class="card-text">{{ $category->description ?: 'لا يوجد وصف' }}</p>
                    
                    <div class="mb-3">
                        <strong>عدد المنتجات:</strong> {{ $category->products_count }}
                    </div>
                    
                    @if($category->parent)
                        <div class="mb-3">
                            <strong>الفئة الأم:</strong> 
                            <a href="{{ route('commerce.smart-categories.analyze', ['categoryId' => $category->parent_id]) }}">
                                {{ $category->parent->name }}
                            </a>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>الفئات الفرعية:</strong> {{ $category->children_count }}
                        @if($category->children_count > 0)
                            <ul class="list-group list-group-flush mt-2">
                                @foreach($category->children as $child)
                                    <li class="list-group-item">
                                        <a href="{{ route('commerce.smart-categories.analyze', ['categoryId' => $child->id]) }}">
                                            {{ $child->name }}
                                        </a>
                                        <span class="badge bg-secondary rounded-pill float-end">{{ $child->products_count }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('commerce.categories.edit', ['id' => $category->id]) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> تعديل الفئة
                        </a>
                        <a href="{{ route('commerce.categories.index') }}?category_id={{ $category->id }}" class="btn btn-outline-primary">
                            <i class="fas fa-box me-1"></i> عرض المنتجات
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- تحليل الفئة -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-brain me-1"></i>
                        تحليل الفئة
                        <span class="ai-badge ms-2">AI</span>
                    </div>
                    <button class="btn btn-sm btn-primary" id="refresh-analysis">
                        <i class="fas fa-sync-alt me-1"></i> تحديث التحليل
                    </button>
                </div>
                <div class="card-body">
                    <div id="analysis-loading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-3">جاري تحليل الفئة باستخدام الذكاء الاصطناعي...</p>
                    </div>
                    
                    <div id="analysis-content">
                        @if(isset($analysis) && $analysis)
                            <!-- السمات والخصائص -->
                            <div class="mb-4">
                                <h5 class="mb-3">السمات والخصائص الرئيسية</h5>
                                <div class="row">
                                    @foreach($analysis['attributes'] as $attribute)
                                        <div class="col-md-6 mb-3">
                                            <div class="card attribute-card">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $attribute['name'] }}</h6>
                                                    <p class="card-text small">{{ $attribute['description'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- الكلمات المفتاحية -->
                            <div class="mb-4">
                                <h5 class="mb-3">الكلمات المفتاحية</h5>
                                <div>
                                    @foreach($analysis['keywords'] as $keyword)
                                        <span class="keyword-tag {{ isset($keyword['is_new']) && $keyword['is_new'] ? 'new' : '' }}">
                                            {{ $keyword['text'] }}
                                            @if(isset($keyword['relevance']))
                                                <span class="ms-1 small text-muted">({{ number_format($keyword['relevance'] * 100, 0) }}%)</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-success" id="generate-keywords-btn" data-category-id="{{ $category->id }}">
                                        <i class="fas fa-magic me-1"></i> توليد كلمات مفتاحية جديدة
                                    </button>
                                </div>
                            </div>
                            
                            <!-- تحسين الوصف -->
                            <div class="mb-4">
                                <h5 class="mb-3">تحسين الوصف</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">الوصف الحالي</label>
                                            <div class="p-3 bg-light rounded">
                                                {{ $category->description ?: 'لا يوجد وصف' }}
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">الوصف المحسن المقترح</label>
                                            <div class="p-3 bg-light rounded">
                                                {{ $analysis['enhanced_description'] }}
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-primary" id="apply-description-btn" 
                                                    data-category-id="{{ $category->id }}" 
                                                    data-description="{{ $analysis['enhanced_description'] }}">
                                                <i class="fas fa-check me-1"></i> تطبيق الوصف المحسن
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- اقتراحات التحسين -->
                            <div class="mb-4">
                                <h5 class="mb-3">اقتراحات التحسين</h5>
                                <div class="list-group">
                                    @foreach($analysis['improvement_suggestions'] as $suggestion)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-1">{{ $suggestion['title'] }}</h6>
                                                <span class="badge bg-primary rounded-pill">{{ $suggestion['priority'] }}</span>
                                            </div>
                                            <p class="mb-1">{{ $suggestion['description'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                لم يتم تحليل هذه الفئة بعد. انقر على زر "تحديث التحليل" لبدء التحليل.
                            </div>
                        @endif
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
        // تحديث التحليل
        $('#refresh-analysis').click(function() {
            const categoryId = '{{ $category->id }}';
            
            // عرض مؤشر التحميل
            $('#analysis-content').hide();
            $('#analysis-loading').show();
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.analyze', ['categoryId' => '_id_']) }}".replace('_id_', categoryId),
                type: 'GET',
                data: { refresh: true },
                success: function(response) {
                    if (response.success) {
                        // تحديث الصفحة لعرض التحليل الجديد
                        location.reload();
                    } else {
                        alert(response.message || 'حدث خطأ أثناء تحليل الفئة');
                        $('#analysis-loading').hide();
                        $('#analysis-content').show();
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                    $('#analysis-loading').hide();
                    $('#analysis-content').show();
                }
            });
        });
        
        // توليد كلمات مفتاحية جديدة
        $('#generate-keywords-btn').click(function() {
            const categoryId = $(this).data('category-id');
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.generate-keywords', ['categoryId' => '_id_']) }}".replace('_id_', categoryId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    // عرض مؤشر التحميل
                    $('#generate-keywords-btn').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التوليد...');
                    $('#generate-keywords-btn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // تحديث الصفحة لعرض الكلمات المفتاحية الجديدة
                        location.reload();
                    } else {
                        alert(response.message || 'حدث خطأ أثناء توليد الكلمات المفتاحية');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                },
                complete: function() {
                    // إخفاء مؤشر التحميل
                    $('#generate-keywords-btn').html('<i class="fas fa-magic me-1"></i> توليد كلمات مفتاحية جديدة');
                    $('#generate-keywords-btn').prop('disabled', false);
                }
            });
        });
        
        // تطبيق الوصف المحسن
        $('#apply-description-btn').click(function() {
            const categoryId = $(this).data('category-id');
            const description = $(this).data('description');
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.enhance-description', ['categoryId' => '_id_']) }}".replace('_id_', categoryId),
                type: 'POST',
                data: {
                    description: description,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    // عرض مؤشر التحميل
                    $('#apply-description-btn').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التطبيق...');
                    $('#apply-description-btn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        alert('تم تطبيق الوصف المحسن بنجاح');
                        // تحديث الصفحة لعرض التغييرات
                        location.reload();
                    } else {
                        alert(response.message || 'حدث خطأ أثناء تطبيق الوصف المحسن');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                },
                complete: function() {
                    // إخفاء مؤشر التحميل
                    $('#apply-description-btn').html('<i class="fas fa-check me-1"></i> تطبيق الوصف المحسن');
                    $('#apply-description-btn').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
