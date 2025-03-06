@extends('layouts.admin')

@section('title', 'قوالب الإشعارات - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">قوالب الإشعارات</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">قوالب الإشعارات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h4 class="card-title flex-grow-1">قوالب الإشعارات المتاحة</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                                <i class="fas fa-plus-circle me-1"></i> إضافة قالب جديد
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="بحث...">
                                <button class="btn btn-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">كل الأنواع</option>
                                <option value="email">بريد إلكتروني</option>
                                <option value="sms">رسائل نصية</option>
                                <option value="push">إشعارات الجوال</option>
                                <option value="database">قاعدة البيانات</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">كل الحالات</option>
                                <option value="1">مفعل</option>
                                <option value="0">معطل</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="templates-table" class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المعرف</th>
                                    <th>اسم القالب</th>
                                    <th>النوع</th>
                                    <th>الموضوع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates ?? [] as $template)
                                <tr>
                                    <td>{{ $template->id }}</td>
                                    <td>{{ $template->name }}</td>
                                    <td>
                                        @if($template->type == 'email')
                                            <span class="badge bg-info">بريد إلكتروني</span>
                                        @elseif($template->type == 'sms')
                                            <span class="badge bg-warning">رسائل نصية</span>
                                        @elseif($template->type == 'push')
                                            <span class="badge bg-success">إشعارات الجوال</span>
                                        @elseif($template->type == 'database')
                                            <span class="badge bg-primary">قاعدة البيانات</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $template->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $template->subject }}</td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge bg-success">مفعل</span>
                                        @else
                                            <span class="badge bg-danger">معطل</span>
                                        @endif
                                    </td>
                                    <td>{{ $template->updated_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="viewTemplate({{ $template->id }})"><i class="fas fa-eye me-2"></i>عرض</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="editTemplate({{ $template->id }})"><i class="fas fa-edit me-2"></i>تعديل</a></li>
                                                <li>
                                                    @if($template->is_active)
                                                        <a class="dropdown-item text-warning" href="#" onclick="toggleTemplateStatus({{ $template->id }}, 'deactivate')"><i class="fas fa-ban me-2"></i>تعطيل</a>
                                                    @else
                                                        <a class="dropdown-item text-success" href="#" onclick="toggleTemplateStatus({{ $template->id }}, 'activate')"><i class="fas fa-check-circle me-2"></i>تفعيل</a>
                                                    @endif
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteTemplate({{ $template->id }})"><i class="fas fa-trash-alt me-2"></i>حذف</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد قوالب إشعارات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($templates) && $templates->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">عرض {{ $templates->firstItem() }} إلى {{ $templates->lastItem() }} من {{ $templates->total() }} قالب</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $templates->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة إضافة قالب جديد -->
<div class="modal fade" id="addTemplateModal" tabindex="-1" aria-labelledby="addTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTemplateModalLabel">إضافة قالب إشعارات جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.notification-templates.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم القالب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">نوع القالب <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">اختر نوع القالب</option>
                                <option value="email">بريد إلكتروني</option>
                                <option value="sms">رسائل نصية</option>
                                <option value="push">إشعارات الجوال</option>
                                <option value="database">قاعدة البيانات</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 email-field">
                        <label for="subject" class="form-label">الموضوع</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">محتوى القالب <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                        <small class="text-muted">يمكنك استخدام المتغيرات مثل {name}، {email}، إلخ.</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">تفعيل القالب</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة عرض قالب -->
<div class="modal fade" id="viewTemplateModal" tabindex="-1" aria-labelledby="viewTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTemplateModalLabel">عرض قالب الإشعارات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>اسم القالب</h5>
                        <p id="view_name"></p>
                    </div>
                    <div class="col-md-6">
                        <h5>النوع</h5>
                        <p id="view_type"></p>
                    </div>
                </div>
                <div class="row mb-3 view-email-field">
                    <div class="col-12">
                        <h5>الموضوع</h5>
                        <p id="view_subject"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <h5>المحتوى</h5>
                        <div id="view_content" class="border p-3 bg-light"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>الحالة</h5>
                        <p id="view_status"></p>
                    </div>
                    <div class="col-md-6">
                        <h5>تاريخ التحديث</h5>
                        <p id="view_updated_at"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تعديل قالب -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" aria-labelledby="editTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTemplateModalLabel">تعديل قالب الإشعارات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTemplateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">اسم القالب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_type" class="form-label">نوع القالب <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="">اختر نوع القالب</option>
                                <option value="email">بريد إلكتروني</option>
                                <option value="sms">رسائل نصية</option>
                                <option value="push">إشعارات الجوال</option>
                                <option value="database">قاعدة البيانات</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 edit-email-field">
                        <label for="edit_subject" class="form-label">الموضوع</label>
                        <input type="text" class="form-control" id="edit_subject" name="subject">
                    </div>
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">محتوى القالب <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_content" name="content" rows="10" required></textarea>
                        <small class="text-muted">يمكنك استخدام المتغيرات مثل {name}، {email}، إلخ.</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">تفعيل القالب</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div class="modal fade" id="deleteTemplateModal" tabindex="-1" aria-labelledby="deleteTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTemplateModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا القالب؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteTemplateForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // إظهار/إخفاء حقول البريد الإلكتروني بناءً على نوع القالب
    document.getElementById('type').addEventListener('change', function() {
        toggleEmailFields(this.value, 'email-field');
    });
    
    document.getElementById('edit_type').addEventListener('change', function() {
        toggleEmailFields(this.value, 'edit-email-field');
    });
    
    function toggleEmailFields(type, className) {
        const emailFields = document.getElementsByClassName(className);
        for (let i = 0; i < emailFields.length; i++) {
            if (type === 'email') {
                emailFields[i].style.display = 'block';
            } else {
                emailFields[i].style.display = 'none';
            }
        }
    }

    // تصفية قوالب الإشعارات
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTemplates();
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        filterTemplates();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTemplates();
    });

    function filterTemplates() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const typeFilter = document.getElementById('typeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        
        const rows = document.querySelectorAll('#templates-table tbody tr');
        
        rows.forEach(row => {
            const templateName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const templateType = row.querySelector('td:nth-child(3) .badge').textContent.toLowerCase();
            const templateSubject = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const templateStatus = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();
            
            const matchesSearch = templateName.includes(searchTerm) || templateSubject.includes(searchTerm);
            const matchesType = !typeFilter || templateType.includes(typeFilter === 'email' ? 'بريد إلكتروني' : (typeFilter === 'sms' ? 'رسائل نصية' : (typeFilter === 'push' ? 'إشعارات الجوال' : (typeFilter === 'database' ? 'قاعدة البيانات' : ''))));
            const matchesStatus = !statusFilter || (statusFilter === '1' ? templateStatus.includes('مفعل') : templateStatus.includes('معطل'));
            
            if (matchesSearch && matchesType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // عرض قالب
    function viewTemplate(templateId) {
        if (!templateId) return;
        
        // استرجاع بيانات القالب من الخادم
        fetch(`/admin/notification-templates/${templateId}`)
            .then(response => response.json())
            .then(data => {
                if (data.template) {
                    const template = data.template;
                    document.getElementById('view_name').textContent = template.name;
                    
                    let typeText = '';
                    if (template.type === 'email') typeText = 'بريد إلكتروني';
                    else if (template.type === 'sms') typeText = 'رسائل نصية';
                    else if (template.type === 'push') typeText = 'إشعارات الجوال';
                    else if (template.type === 'database') typeText = 'قاعدة البيانات';
                    else typeText = template.type;
                    
                    document.getElementById('view_type').textContent = typeText;
                    document.getElementById('view_subject').textContent = template.subject || 'غير محدد';
                    document.getElementById('view_content').innerHTML = template.content.replace(/\n/g, '<br>');
                    document.getElementById('view_status').textContent = template.is_active ? 'مفعل' : 'معطل';
                    document.getElementById('view_updated_at').textContent = new Date(template.updated_at).toLocaleString('ar-SA');
                    
                    // إظهار/إخفاء حقول البريد الإلكتروني
                    const viewEmailFields = document.getElementsByClassName('view-email-field');
                    for (let i = 0; i < viewEmailFields.length; i++) {
                        viewEmailFields[i].style.display = template.type === 'email' ? 'block' : 'none';
                    }
                    
                    const viewModal = new bootstrap.Modal(document.getElementById('viewTemplateModal'));
                    viewModal.show();
                } else {
                    alert('حدث خطأ أثناء استرجاع بيانات القالب');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء استرجاع بيانات القالب');
            });
    }

    // تعديل قالب
    function editTemplate(templateId) {
        if (!templateId) return;
        
        // استرجاع بيانات القالب من الخادم
        fetch(`/admin/notification-templates/${templateId}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.template) {
                    const template = data.template;
                    document.getElementById('edit_name').value = template.name;
                    document.getElementById('edit_type').value = template.type;
                    document.getElementById('edit_subject').value = template.subject || '';
                    document.getElementById('edit_content').value = template.content;
                    document.getElementById('edit_is_active').checked = template.is_active;
                    
                    // إظهار/إخفاء حقول البريد الإلكتروني
                    toggleEmailFields(template.type, 'edit-email-field');
                    
                    const editForm = document.getElementById('editTemplateForm');
                    editForm.action = `/admin/notification-templates/${templateId}`;
                    
                    const editModal = new bootstrap.Modal(document.getElementById('editTemplateModal'));
                    editModal.show();
                } else {
                    alert('حدث خطأ أثناء استرجاع بيانات القالب');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء استرجاع بيانات القالب');
            });
    }

    // تبديل حالة القالب (تفعيل/تعطيل)
    function toggleTemplateStatus(templateId, action) {
        if (!templateId) return;
        
        let url = '';
        let confirmMessage = '';
        
        if (action === 'activate') {
            url = `/admin/notification-templates/${templateId}/activate`;
            confirmMessage = 'هل أنت متأكد من رغبتك في تفعيل هذا القالب؟';
        } else if (action === 'deactivate') {
            url = `/admin/notification-templates/${templateId}/deactivate`;
            confirmMessage = 'هل أنت متأكد من رغبتك في تعطيل هذا القالب؟';
        } else {
            return;
        }
        
        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PUT';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // حذف قالب
    function deleteTemplate(templateId) {
        if (!templateId) return;
        
        const deleteForm = document.getElementById('deleteTemplateForm');
        deleteForm.action = `/admin/notification-templates/${templateId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteTemplateModal'));
        deleteModal.show();
    }

    // تهيئة الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // إخفاء حقول البريد الإلكتروني عند التحميل إذا لم يكن نوع القالب بريد إلكتروني
        toggleEmailFields(document.getElementById('type').value, 'email-field');
    });
</script>
@endsection