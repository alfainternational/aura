@extends('layouts.app')

@section('title', 'التنقل الرئيسي والقوائم')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">التنقل الرئيسي والقوائم</h1>
            <p class="text-muted">تخصيص قوائم التنقل وإدارة الروابط المفضلة</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- القائمة الرئيسية -->
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <h5 class="mb-0">القائمة الرئيسية</h5>
                </x-slot>
                
                <div class="list-group list-group-flush" id="mainMenuItems">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-house text-primary me-2"></i>
                            <span>الصفحة الرئيسية</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary me-1">أساسي</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-person text-primary me-2"></i>
                            <span>الملف الشخصي</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary me-1">أساسي</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-bell text-primary me-2"></i>
                            <span>الإشعارات</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary me-1">أساسي</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-gear text-primary me-2"></i>
                            <span>الإعدادات</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary me-1">أساسي</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <p class="text-muted small">لا يمكن تعديل أو إزالة العناصر الأساسية من القائمة الرئيسية</p>
                </div>
            </x-card>
            
            <!-- الروابط المفضلة -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الروابط المفضلة</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFavoriteModal">
                            <i class="bi bi-plus"></i> إضافة
                        </button>
                    </div>
                </x-slot>
                
                <div class="favorites-container">
                    <!-- حالة عدم وجود روابط مفضلة -->
                    <div class="text-center py-4">
                        <i class="bi bi-bookmark-star fs-3 d-block mb-2 text-muted"></i>
                        <p class="text-muted mb-0">لم تقم بإضافة أي روابط مفضلة بعد</p>
                        <p class="text-muted small">أضف روابطك المفضلة للوصول السريع إليها</p>
                    </div>
                    
                    <!-- نموذج للروابط المفضلة (معطل حالياً) -->
                    <div class="list-group list-group-flush d-none" id="favoriteLinks">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-link-45deg text-primary me-2"></i>
                                <span>اسم الرابط المفضل</span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="col-md-8">
            <!-- تخصيص القائمة الجانبية -->
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تخصيص القائمة الجانبية</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="resetSidebar">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="saveSidebar">
                                <i class="bi bi-save me-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </x-slot>
                
                <p class="text-muted small mb-3">اسحب وأفلت العناصر لإعادة ترتيبها. يمكنك أيضًا تفعيل أو تعطيل العناصر حسب احتياجاتك.</p>
                
                <div class="sidebar-items-container">
                    <div class="list-group" id="sidebarItems">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item1" checked>
                                    <label class="form-check-label" for="item1">
                                        <i class="bi bi-house text-primary me-2"></i> الصفحة الرئيسية
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-primary">أساسي</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item2" checked>
                                    <label class="form-check-label" for="item2">
                                        <i class="bi bi-graph-up text-success me-2"></i> الإحصائيات
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-secondary">اختياري</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item3" checked>
                                    <label class="form-check-label" for="item3">
                                        <i class="bi bi-bell text-warning me-2"></i> الإشعارات
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-primary">أساسي</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item4" checked>
                                    <label class="form-check-label" for="item4">
                                        <i class="bi bi-person text-info me-2"></i> الملف الشخصي
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-primary">أساسي</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item5">
                                    <label class="form-check-label" for="item5">
                                        <i class="bi bi-chat-dots text-primary me-2"></i> الرسائل
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-secondary">اختياري</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle"></i>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="item6" checked>
                                    <label class="form-check-label" for="item6">
                                        <i class="bi bi-gear text-secondary me-2"></i> الإعدادات
                                    </label>
                                </div>
                            </div>
                            <span class="badge bg-primary">أساسي</span>
                        </div>
                    </div>
                </div>
            </x-card>
            
            <!-- الوصول السريع -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">الوصول السريع</h5>
                </x-slot>
                
                <p class="text-muted small mb-3">حدد العناصر التي تريد عرضها في قسم الوصول السريع في لوحة التحكم.</p>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess1" checked>
                            <label class="form-check-label" for="quickAccess1">
                                <i class="bi bi-graph-up text-success me-2"></i> الإحصائيات السريعة
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess2" checked>
                            <label class="form-check-label" for="quickAccess2">
                                <i class="bi bi-clock-history text-info me-2"></i> آخر النشاطات
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess3" checked>
                            <label class="form-check-label" for="quickAccess3">
                                <i class="bi bi-bell text-warning me-2"></i> الإشعارات الأخيرة
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess4">
                            <label class="form-check-label" for="quickAccess4">
                                <i class="bi bi-calendar-event text-primary me-2"></i> المواعيد القادمة
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess5">
                            <label class="form-check-label" for="quickAccess5">
                                <i class="bi bi-chat-dots text-success me-2"></i> الرسائل الأخيرة
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quickAccess6">
                            <label class="form-check-label" for="quickAccess6">
                                <i class="bi bi-bookmark-star text-danger me-2"></i> الروابط المفضلة
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" id="saveQuickAccess">
                        <i class="bi bi-save me-1"></i> حفظ التفضيلات
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</div>

<!-- نافذة إضافة رابط مفضل -->
<x-modal id="addFavoriteModal" title="إضافة رابط مفضل">
    <form id="addFavoriteForm">
        <div class="mb-3">
            <label for="favoriteTitle" class="form-label">عنوان الرابط</label>
            <input type="text" class="form-control" id="favoriteTitle" placeholder="أدخل عنوان الرابط المفضل">
        </div>
        
        <div class="mb-3">
            <label for="favoriteUrl" class="form-label">الرابط</label>
            <input type="url" class="form-control" id="favoriteUrl" placeholder="https://example.com">
        </div>
        
        <div class="mb-3">
            <label for="favoriteIcon" class="form-label">الأيقونة</label>
            <select class="form-select" id="favoriteIcon">
                <option value="bi-link">رابط</option>
                <option value="bi-globe">موقع ويب</option>
                <option value="bi-file-earmark">ملف</option>
                <option value="bi-folder">مجلد</option>
                <option value="bi-star">نجمة</option>
                <option value="bi-heart">قلب</option>
                <option value="bi-bookmark">إشارة مرجعية</option>
            </select>
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-primary">إضافة</button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // يمكن إضافة كود JavaScript لتنفيذ وظائف السحب والإفلات وحفظ التفضيلات
        
        // مثال على معالجة نموذج إضافة المفضلة
        const addFavoriteForm = document.getElementById('addFavoriteForm');
        if (addFavoriteForm) {
            addFavoriteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // هنا يمكن إضافة كود لحفظ الرابط المفضل
                
                // إغلاق النافذة المنبثقة بعد الإضافة
                const modal = bootstrap.Modal.getInstance(document.getElementById('addFavoriteModal'));
                modal.hide();
                
                // عرض رسالة نجاح
                alert('تمت إضافة الرابط المفضل بنجاح!');
            });
        }
        
        // أزرار حفظ التفضيلات
        const saveSidebarBtn = document.getElementById('saveSidebar');
        const saveQuickAccessBtn = document.getElementById('saveQuickAccess');
        
        if (saveSidebarBtn) {
            saveSidebarBtn.addEventListener('click', function() {
                // هنا يمكن إضافة كود لحفظ تفضيلات القائمة الجانبية
                alert('تم حفظ تفضيلات القائمة الجانبية بنجاح!');
            });
        }
        
        if (saveQuickAccessBtn) {
            saveQuickAccessBtn.addEventListener('click', function() {
                // هنا يمكن إضافة كود لحفظ تفضيلات الوصول السريع
                alert('تم حفظ تفضيلات الوصول السريع بنجاح!');
            });
        }
    });
</script>
@endpush
@endsection
