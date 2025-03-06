@extends('layouts.admin')

@section('title', 'التصنيفات الذكية المدعومة بالذكاء الاصطناعي')

@section('styles')
<style>
    .category-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    .ai-badge {
        background: linear-gradient(45deg, #6b46c1, #805ad5);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
    }
    .confidence-high {
        background-color: #48bb78;
    }
    .confidence-medium {
        background-color: #ecc94b;
    }
    .confidence-low {
        background-color: #f56565;
    }
    .suggestion-item {
        border-left: 4px solid #805ad5;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">التصنيفات الذكية المدعومة بالذكاء الاصطناعي</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
        <li class="breadcrumb-item"><a href="{{ route('commerce.categories.index') }}">التصنيفات</a></li>
        <li class="breadcrumb-item active">التصنيفات الذكية</li>
    </ol>

    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card category-card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">إجمالي الفئات</div>
                            <div class="display-6">{{ $totalCategories }}</div>
                        </div>
                        <i class="fas fa-folder fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('commerce.categories.index') }}">عرض التفاصيل</a>
                    <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card category-card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">الفئات النشطة</div>
                            <div class="display-6">{{ $activeCategories }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('commerce.categories.index') }}?status=active">عرض التفاصيل</a>
                    <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card category-card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">منتجات بدون تصنيف</div>
                            <div class="display-6">{{ $productsWithoutCategory }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#" id="auto-classify-btn">تصنيف تلقائي</a>
                    <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card category-card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">اقتراحات إعادة التنظيم</div>
                            <div class="display-6">{{ count($reorganizationSuggestions) }}</div>
                        </div>
                        <i class="fas fa-sitemap fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#reorganization-section">عرض الاقتراحات</a>
                    <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- أدوات التصنيف الذكي -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-brain me-1"></i>
                أدوات التصنيف الذكي
                <span class="ai-badge ms-2">AI</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-magic me-1"></i>
                            اقتراح فئات للمنتج
                        </div>
                        <div class="card-body">
                            <p>استخدم الذكاء الاصطناعي لاقتراح الفئات المناسبة لمنتج معين بناءً على اسمه ووصفه ومواصفاته.</p>
                            <form id="suggest-categories-form">
                                <div class="mb-3">
                                    <label for="product-id" class="form-label">اختر منتجًا</label>
                                    <select class="form-select" id="product-id" name="product_id">
                                        <option value="">-- اختر منتجًا --</option>
                                        <!-- سيتم ملء هذه القائمة بالمنتجات عبر JavaScript -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="limit" class="form-label">عدد الاقتراحات</label>
                                    <input type="number" class="form-control" id="limit" name="limit" min="1" max="10" value="5">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lightbulb me-1"></i> اقتراح الفئات
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-layer-group me-1"></i>
                            توليد فئات جديدة
                        </div>
                        <div class="card-body">
                            <p>استخدم الذكاء الاصطناعي لتحليل مجموعة من المنتجات واقتراح فئات جديدة مناسبة لها.</p>
                            <form id="generate-categories-form">
                                <div class="mb-3">
                                    <label class="form-label">اختر المنتجات</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="products-source" id="uncategorized-products" value="uncategorized" checked>
                                        <label class="form-check-label" for="uncategorized-products">
                                            المنتجات غير المصنفة ({{ $productsWithoutCategory }})
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="products-source" id="select-products" value="select">
                                        <label class="form-check-label" for="select-products">
                                            اختيار منتجات محددة
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3" id="product-selection" style="display: none;">
                                    <select class="form-select" id="selected-products" name="product_ids[]" multiple>
                                        <!-- سيتم ملء هذه القائمة بالمنتجات عبر JavaScript -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="max-categories" class="form-label">الحد الأقصى للفئات المقترحة</label>
                                    <input type="number" class="form-control" id="max-categories" name="max_categories" min="1" max="20" value="10">
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus-circle me-1"></i> توليد فئات جديدة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- إضافة قسم تحليل الفئات -->
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            تحليل الفئات
                        </div>
                        <div class="card-body">
                            <p>استخدم الذكاء الاصطناعي لتحليل خصائص الفئات الحالية وتحسين وصفها وتوليد كلمات مفتاحية لها.</p>
                            <div class="mb-3">
                                <label for="category-to-analyze" class="form-label">اختر فئة للتحليل</label>
                                <select class="form-select" id="category-to-analyze">
                                    <option value="">-- اختر فئة --</option>
                                    <!-- سيتم ملء هذه القائمة بالفئات عبر JavaScript -->
                                </select>
                            </div>
                            <button type="button" id="analyze-category-btn" class="btn btn-primary" disabled>
                                <i class="fas fa-microscope me-1"></i> تحليل الفئة
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-robot me-1"></i>
                            التصنيف التلقائي
                        </div>
                        <div class="card-body">
                            <p>استخدم الذكاء الاصطناعي لتصنيف المنتجات غير المصنفة تلقائيًا إلى الفئات المناسبة.</p>
                            <div class="mb-3">
                                <label for="confidence-threshold" class="form-label">حد الثقة ({{ number_format(0.7 * 100, 0) }}%)</label>
                                <input type="range" class="form-range" id="confidence-threshold" min="0.5" max="0.95" step="0.05" value="0.7">
                                <div class="form-text">كلما زاد حد الثقة، كلما قل عدد المنتجات التي سيتم تصنيفها تلقائيًا ولكن بدقة أعلى.</div>
                            </div>
                            <button type="button" id="auto-classify-btn" class="btn btn-primary">
                                <i class="fas fa-tasks me-1"></i> تصنيف تلقائي
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- اقتراحات إعادة التنظيم -->
    <div class="card mb-4" id="reorganization-section">
        <div class="card-header">
            <i class="fas fa-sitemap me-1"></i>
            اقتراحات إعادة تنظيم الفئات
            <span class="ai-badge ms-2">AI</span>
        </div>
        <div class="card-body">
            @if(count($reorganizationSuggestions) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>الاقتراح</th>
                                <th>المنتجات المتأثرة</th>
                                <th>الفئة المقترحة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reorganizationSuggestions as $suggestion)
                                <tr>
                                    <td>{{ $suggestion['description'] }}</td>
                                    <td>{{ count($suggestion['product_ids']) }}</td>
                                    <td>{{ $suggestion['target_category_name'] }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary apply-suggestion" 
                                                data-suggestion-id="{{ $suggestion['id'] }}"
                                                data-product-ids="{{ json_encode($suggestion['product_ids']) }}"
                                                data-target-category-id="{{ $suggestion['target_category_id'] }}">
                                            تطبيق
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    لا توجد اقتراحات لإعادة تنظيم الفئات حاليًا.
                </div>
            @endif
        </div>
    </div>

    <!-- الفئات المتشابهة -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-clone me-1"></i>
            الفئات المتشابهة المرشحة للدمج
            <span class="ai-badge ms-2">AI</span>
        </div>
        <div class="card-body">
            @if(count($similarCategories) > 0)
                <div class="row">
                    @foreach($similarCategories as $pair)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">فئات متشابهة ({{ number_format($pair['similarity_score'] * 100, 0) }}% تشابه)</h5>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="border p-2 rounded mb-2">
                                                <strong>{{ $pair['category1']['name'] }}</strong>
                                                <p class="small text-muted mb-0">{{ Str::limit($pair['category1']['description'], 100) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrows-alt-h"></i>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="border p-2 rounded mb-2">
                                                <strong>{{ $pair['category2']['name'] }}</strong>
                                                <p class="small text-muted mb-0">{{ Str::limit($pair['category2']['description'], 100) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <button class="btn btn-sm btn-warning merge-categories"
                                                data-source-id="{{ $pair['category1']['id'] }}"
                                                data-target-id="{{ $pair['category2']['id'] }}">
                                            <i class="fas fa-object-group me-1"></i> دمج في {{ $pair['category2']['name'] }}
                                        </button>
                                        <a href="{{ route('commerce.smart-categories.analyze', ['categoryId' => $pair['category1']['id']]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-search me-1"></i> تحليل {{ $pair['category1']['name'] }}
                                        </a>
                                        <a href="{{ route('commerce.smart-categories.analyze', ['categoryId' => $pair['category2']['id']]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-search me-1"></i> تحليل {{ $pair['category2']['name'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    لم يتم العثور على فئات متشابهة يمكن دمجها.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- نافذة منبثقة لعرض نتائج اقتراح الفئات -->
<div class="modal fade" id="suggestions-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الفئات المقترحة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="suggestions-container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- نافذة منبثقة لعرض الفئات المقترحة الجديدة -->
<div class="modal fade" id="new-categories-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الفئات الجديدة المقترحة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="new-categories-container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="create-selected-categories">إنشاء الفئات المحددة</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحميل المنتجات للقوائم المنسدلة
        loadProducts();
        
        // تحميل الفئات للقائمة المنسدلة
        loadCategories();
        
        // تبديل عرض اختيار المنتجات
        $('input[name="products-source"]').change(function() {
            if ($(this).val() === 'select') {
                $('#product-selection').show();
            } else {
                $('#product-selection').hide();
            }
        });
        
        // نموذج اقتراح الفئات
        $('#suggest-categories-form').submit(function(e) {
            e.preventDefault();
            const productId = $('#product-id').val();
            const limit = $('#limit').val();
            
            if (!productId) {
                alert('الرجاء اختيار منتج');
                return;
            }
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.suggest-categories') }}",
                type: 'POST',
                data: {
                    product_id: productId,
                    limit: limit,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    // عرض مؤشر التحميل
                    $('#suggest-categories-form button').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التحليل...');
                    $('#suggest-categories-form button').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        displaySuggestions(response.suggestions);
                    } else {
                        alert(response.message || 'حدث خطأ أثناء اقتراح الفئات');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                },
                complete: function() {
                    // إخفاء مؤشر التحميل
                    $('#suggest-categories-form button').html('<i class="fas fa-lightbulb me-1"></i> اقتراح الفئات');
                    $('#suggest-categories-form button').prop('disabled', false);
                }
            });
        });
        
        // نموذج توليد الفئات الجديدة
        $('#generate-categories-form').submit(function(e) {
            e.preventDefault();
            const source = $('input[name="products-source"]:checked').val();
            const maxCategories = $('#max-categories').val();
            let data = {
                max_categories: maxCategories,
                _token: "{{ csrf_token() }}"
            };
            
            if (source === 'select') {
                const productIds = $('#selected-products').val();
                if (!productIds || productIds.length === 0) {
                    alert('الرجاء اختيار منتج واحد على الأقل');
                    return;
                }
                data.product_ids = productIds;
            }
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.generate-categories') }}",
                type: 'POST',
                data: data,
                beforeSend: function() {
                    // عرض مؤشر التحميل
                    $('#generate-categories-form button').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التحليل...');
                    $('#generate-categories-form button').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        displayNewCategories(response.suggested_categories, response.analyzed_products_count);
                    } else {
                        alert(response.message || 'حدث خطأ أثناء توليد الفئات');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                },
                complete: function() {
                    // إخفاء مؤشر التحميل
                    $('#generate-categories-form button').html('<i class="fas fa-plus-circle me-1"></i> توليد فئات جديدة');
                    $('#generate-categories-form button').prop('disabled', false);
                }
            });
        });
        
        // زر التصنيف التلقائي
        $('#auto-classify-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('هل أنت متأكد من رغبتك في تصنيف المنتجات تلقائيًا؟ سيتم تحليل المنتجات غير المصنفة وتعيينها للفئات المناسبة.')) {
                $.ajax({
                    url: "{{ route('commerce.smart-categories.auto-classify') }}",
                    type: 'POST',
                    data: {
                        confidence_threshold: 0.7,
                        limit: 50,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        // عرض مؤشر التحميل
                        $('#auto-classify-btn').html('<i class="fas fa-spinner fa-spin"></i>');
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // تحديث الصفحة لعرض التغييرات
                            location.reload();
                        } else {
                            alert(response.message || 'حدث خطأ أثناء التصنيف التلقائي');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل
                        $('#auto-classify-btn').html('تصنيف تلقائي');
                    }
                });
            }
        });
        
        // تطبيق اقتراح إعادة التنظيم
        $('.apply-suggestion').click(function() {
            const suggestionId = $(this).data('suggestion-id');
            const productIds = $(this).data('product-ids');
            const targetCategoryId = $(this).data('target-category-id');
            
            if (confirm('هل أنت متأكد من رغبتك في تطبيق هذا الاقتراح؟')) {
                $.ajax({
                    url: "{{ route('commerce.smart-categories.apply-reorganization') }}",
                    type: 'POST',
                    data: {
                        suggestion_id: suggestionId,
                        product_ids: productIds,
                        target_category_id: targetCategoryId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        // عرض مؤشر التحميل
                        $(this).html('<i class="fas fa-spinner fa-spin"></i>');
                        $(this).prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // تحديث الصفحة لعرض التغييرات
                            location.reload();
                        } else {
                            alert(response.message || 'حدث خطأ أثناء تطبيق الاقتراح');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل
                        $(this).html('تطبيق');
                        $(this).prop('disabled', false);
                    }
                });
            }
        });
        
        // دمج الفئات المتشابهة
        $('.merge-categories').click(function() {
            const sourceId = $(this).data('source-id');
            const targetId = $(this).data('target-id');
            
            if (confirm('هل أنت متأكد من رغبتك في دمج هاتين الفئتين؟ سيتم نقل جميع المنتجات والفئات الفرعية من الفئة المصدر إلى الفئة الهدف.')) {
                $.ajax({
                    url: "{{ route('commerce.smart-categories.merge-categories') }}",
                    type: 'POST',
                    data: {
                        source_category_id: sourceId,
                        target_category_id: targetId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        // عرض مؤشر التحميل
                        $(this).html('<i class="fas fa-spinner fa-spin"></i>');
                        $(this).prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // تحديث الصفحة لعرض التغييرات
                            location.reload();
                        } else {
                            alert(response.message || 'حدث خطأ أثناء دمج الفئات');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل
                        $(this).html('دمج');
                        $(this).prop('disabled', false);
                    }
                });
            }
        });
        
        // تحميل الفئات للقائمة المنسدلة
        function loadCategories() {
            $.ajax({
                url: "{{ route('commerce.categories.list.json') }}",
                type: 'GET',
                success: function(categories) {
                    let options = '';
                    
                    categories.forEach(function(category) {
                        options += `<option value="${category.id}">${category.name}</option>`;
                    });
                    
                    $('#category-id').append(options);
                    $('#target-category-id').append(options);
                    $('#category-to-analyze').append(options);
                    
                    // تفعيل القائمة المنسدلة للتحليل
                    $('#category-to-analyze').on('change', function() {
                        $('#analyze-category-btn').prop('disabled', !$(this).val());
                    });
                }
            });
        }
        
        // تحميل المنتجات للقوائم المنسدلة
        function loadProducts() {
            $.ajax({
                url: "{{ route('commerce.products.list.json') }}",
                type: 'GET',
                success: function(products) {
                    let options = '';
                    
                    products.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    
                    $('#product-id').append(options);
                    $('#product-ids').append(options);
                    
                    // تفعيل القائمة المتعددة
                    $('#selected-products').select2({
                        placeholder: 'اختر المنتجات',
                        allowClear: true
                    });
                }
            });
        }
        
        // زر تحليل الفئة
        $('#analyze-category-btn').click(function() {
            const categoryId = $('#category-to-analyze').val();
            
            if (categoryId) {
                window.location.href = "{{ route('commerce.smart-categories.analyze', ['categoryId' => '_id_']) }}".replace('_id_', categoryId);
            }
        });
        
        // نموذج اقتراح الفئات
        $('#suggest-categories-form').submit(function(e) {
            e.preventDefault();
            const productId = $('#product-id').val();
            const limit = $('#limit').val();
            
            if (!productId) {
                alert('الرجاء اختيار منتج');
                return;
            }
            
            $.ajax({
                url: "{{ route('commerce.smart-categories.suggest-categories') }}",
                type: 'POST',
                data: {
                    product_id: productId,
                    limit: limit,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    // عرض مؤشر التحميل
                    $('#suggest-categories-form button').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التحليل...');
                    $('#suggest-categories-form button').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        displaySuggestions(response.suggestions);
                    } else {
                        alert(response.message || 'حدث خطأ أثناء اقتراح الفئات');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء الاتصال بالخادم');
                },
                complete: function() {
                    // إخفاء مؤشر التحميل
                    $('#suggest-categories-form button').html('<i class="fas fa-lightbulb me-1"></i> اقتراح الفئات');
                    $('#suggest-categories-form button').prop('disabled', false);
                }
            });
        });
        
        // عرض اقتراحات الفئات
        function displaySuggestions(suggestions) {
            let html = '';
            
            if (suggestions.length === 0) {
                html = '<div class="alert alert-info">لم يتم العثور على فئات مقترحة.</div>';
            } else {
                html = '<div class="list-group">';
                
                suggestions.forEach(function(suggestion) {
                    let confidenceClass = 'confidence-low';
                    if (suggestion.confidence >= 0.8) {
                        confidenceClass = 'confidence-high';
                    } else if (suggestion.confidence >= 0.5) {
                        confidenceClass = 'confidence-medium';
                    }
                    
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1">${suggestion.name}</h5>
                                <span class="badge ${confidenceClass} rounded-pill">${Math.round(suggestion.confidence * 100)}%</span>
                            </div>
                            ${suggestion.path ? `
                                <p class="mb-1 small">
                                    <i class="fas fa-sitemap me-1"></i> 
                                    ${suggestion.path.map(p => p.name).join(' &raquo; ')}
                                </p>
                            ` : ''}
                            ${suggestion.product_count ? `
                                <p class="mb-1 small">
                                    <i class="fas fa-box me-1"></i> 
                                    ${suggestion.product_count} منتج
                                </p>
                            ` : ''}
                            <div class="mt-2">
                                <a href="{{ route('commerce.categories.index') }}?category_id=${suggestion.category_id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> عرض الفئة
                                </a>
                                <button class="btn btn-sm btn-outline-success apply-category" data-category-id="${suggestion.category_id}" data-product-id="${suggestion.product_id}">
                                    <i class="fas fa-check me-1"></i> تطبيق
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            $('#suggestions-container').html(html);
            $('#suggestions-modal').modal('show');
            
            // تطبيق الفئة على المنتج
            $('.apply-category').click(function() {
                const categoryId = $(this).data('category-id');
                const productId = $(this).data('product-id');
                
                $.ajax({
                    url: "{{ route('commerce.products.update', ['id' => '_id_']) }}".replace('_id_', productId),
                    type: 'PUT',
                    data: {
                        category_id: categoryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('تم تطبيق الفئة بنجاح');
                            $('#suggestions-modal').modal('hide');
                        } else {
                            alert(response.message || 'حدث خطأ أثناء تطبيق الفئة');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    }
                });
            });
        }
        
        // عرض الفئات الجديدة المقترحة
        function displayNewCategories(categories, analyzedCount) {
            let html = '';
            
            if (categories.length === 0) {
                html = '<div class="alert alert-info">لم يتم العثور على فئات جديدة مقترحة.</div>';
            } else {
                html = `
                    <div class="alert alert-info">
                        تم تحليل ${analyzedCount} منتج واقتراح ${categories.length} فئة جديدة.
                    </div>
                    <div class="list-group">
                `;
                
                categories.forEach(function(category, index) {
                    html += `
                        <div class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input category-checkbox" type="checkbox" value="${index}" id="category-${index}" checked>
                                <label class="form-check-label" for="category-${index}">
                                    <h5 class="mb-1">${category.name}</h5>
                                </label>
                            </div>
                            <p class="mb-1">${category.description}</p>
                            ${category.parent_id ? `
                                <p class="mb-1 small">
                                    <i class="fas fa-level-up-alt me-1"></i> 
                                    الفئة الأم: ${category.parent_name}
                                </p>
                            ` : ''}
                            <p class="mb-1 small">
                                <i class="fas fa-box me-1"></i> 
                                المنتجات المقترحة: ${category.product_ids.length}
                            </p>
                            ${category.keywords ? `
                                <p class="mb-1 small">
                                    <i class="fas fa-tags me-1"></i> 
                                    الكلمات المفتاحية: ${category.keywords.join(', ')}
                                </p>
                            ` : ''}
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            $('#new-categories-container').html(html);
            $('#new-categories-modal').modal('show');
            
            // إنشاء الفئات المحددة
            $('#create-selected-categories').click(function() {
                const selectedCategories = [];
                
                $('.category-checkbox:checked').each(function() {
                    const index = $(this).val();
                    selectedCategories.push(categories[index]);
                });
                
                if (selectedCategories.length === 0) {
                    alert('الرجاء تحديد فئة واحدة على الأقل');
                    return;
                }
                
                $.ajax({
                    url: "{{ route('commerce.categories.store') }}",
                    type: 'POST',
                    data: {
                        categories: selectedCategories,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        // عرض مؤشر التحميل
                        $('#create-selected-categories').html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الإنشاء...');
                        $('#create-selected-categories').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#new-categories-modal').modal('hide');
                            // تحديث الصفحة لعرض التغييرات
                            location.reload();
                        } else {
                            alert(response.message || 'حدث خطأ أثناء إنشاء الفئات');
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    },
                    complete: function() {
                        // إخفاء مؤشر التحميل
                        $('#create-selected-categories').html('إنشاء الفئات المحددة');
                        $('#create-selected-categories').prop('disabled', false);
                    }
                });
            });
        }
    });
</script>
@endsection