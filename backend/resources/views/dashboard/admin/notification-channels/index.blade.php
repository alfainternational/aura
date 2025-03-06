@extends('layouts.admin')

@section('title', 'قنوات الإشعارات - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">قنوات الإشعارات</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">قنوات الإشعارات</li>
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
                        <h4 class="card-title flex-grow-1">قنوات الإشعارات المتاحة</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChannelModal">
                                <i class="fas fa-plus-circle me-1"></i> إضافة قناة جديدة
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="channels-table" class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المعرف</th>
                                    <th>اسم القناة</th>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($channels ?? [] as $channel)
                                <tr>
                                    <td>{{ $channel->id }}</td>
                                    <td>{{ $channel->name }}</td>
                                    <td>
                                        @if($channel->type == 'email')
                                            <span class="badge bg-info">بريد إلكتروني</span>
                                        @elseif($channel->type == 'sms')
                                            <span class="badge bg-warning">رسائل نصية</span>
                                        @elseif($channel->type == 'push')
                                            <span class="badge bg-success">إشعارات الجوال</span>
                                        @elseif($channel->type == 'database')
                                            <span class="badge bg-primary">قاعدة البيانات</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $channel->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $channel->description }}</td>
                                    <td>
                                        @if($channel->is_active)
                                            <span class="badge bg-success">مفعل</span>
                                        @else
                                            <span class="badge bg-danger">معطل</span>
                                        @endif
                                    </td>
                                    <td>{{ $channel->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="editChannel({{ $channel->id }})"><i class="fas fa-edit me-2"></i>تعديل</a></li>
                                                <li>
                                                    @if($channel->is_active)
                                                        <a class="dropdown-item text-warning" href="#" onclick="toggleChannelStatus({{ $channel->id }}, 'deactivate')"><i class="fas fa-ban me-2"></i>تعطيل</a>
                                                    @else
                                                        <a class="dropdown-item text-success" href="#" onclick="toggleChannelStatus({{ $channel->id }}, 'activate')"><i class="fas fa-check-circle me-2"></i>تفعيل</a>
                                                    @endif
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteChannel({{ $channel->id }})"><i class="fas fa-trash-alt me-2"></i>حذف</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد قنوات إشعارات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($channels) && $channels->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">عرض {{ $channels->firstItem() }} إلى {{ $channels->lastItem() }} من {{ $channels->total() }} قناة</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $channels->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة إضافة قناة جديدة -->
<div class="modal fade" id="addChannelModal" tabindex="-1" aria-labelledby="addChannelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChannelModalLabel">إضافة قناة إشعارات جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.notification-channels.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم القناة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">نوع القناة <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">اختر نوع القناة</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="sms">رسائل نصية</option>
                            <option value="push">إشعارات الجوال</option>
                            <option value="database">قاعدة البيانات</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="config" class="form-label">إعدادات القناة (JSON)</label>
                        <textarea class="form-control" id="config" name="config" rows="5" placeholder='{"key": "value"}'></textarea>
                        <small class="text-muted">أدخل إعدادات القناة بتنسيق JSON</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">تفعيل القناة</label>
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

<!-- نافذة تعديل قناة -->
<div class="modal fade" id="editChannelModal" tabindex="-1" aria-labelledby="editChannelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editChannelModalLabel">تعديل قناة الإشعارات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editChannelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">اسم القناة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">نوع القناة <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="">اختر نوع القناة</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="sms">رسائل نصية</option>
                            <option value="push">إشعارات الجوال</option>
                            <option value="database">قاعدة البيانات</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_config" class="form-label">إعدادات القناة (JSON)</label>
                        <textarea class="form-control" id="edit_config" name="config" rows="5" placeholder='{"key": "value"}'></textarea>
                        <small class="text-muted">أدخل إعدادات القناة بتنسيق JSON</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">تفعيل القناة</label>
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
<div class="modal fade" id="deleteChannelModal" tabindex="-1" aria-labelledby="deleteChannelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteChannelModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذه القناة؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteChannelForm" method="POST">
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
    // تعديل قناة
    function editChannel(channelId) {
        if (!channelId) return;
        
        // استرجاع بيانات القناة من الخادم
        fetch(`/admin/notification-channels/${channelId}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.channel) {
                    const channel = data.channel;
                    document.getElementById('edit_name').value = channel.name;
                    document.getElementById('edit_type').value = channel.type;
                    document.getElementById('edit_description').value = channel.description;
                    document.getElementById('edit_config').value = JSON.stringify(channel.config, null, 2);
                    document.getElementById('edit_is_active').checked = channel.is_active;
                    
                    const editForm = document.getElementById('editChannelForm');
                    editForm.action = `/admin/notification-channels/${channelId}`;
                    
                    const editModal = new bootstrap.Modal(document.getElementById('editChannelModal'));
                    editModal.show();
                } else {
                    alert('حدث خطأ أثناء استرجاع بيانات القناة');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء استرجاع بيانات القناة');
            });
    }

    // تبديل حالة القناة (تفعيل/تعطيل)
    function toggleChannelStatus(channelId, action) {
        if (!channelId) return;
        
        let url = '';
        let confirmMessage = '';
        
        if (action === 'activate') {
            url = `/admin/notification-channels/${channelId}/activate`;
            confirmMessage = 'هل أنت متأكد من رغبتك في تفعيل هذه القناة؟';
        } else if (action === 'deactivate') {
            url = `/admin/notification-channels/${channelId}/deactivate`;
            confirmMessage = 'هل أنت متأكد من رغبتك في تعطيل هذه القناة؟';
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

    // حذف قناة
    function deleteChannel(channelId) {
        if (!channelId) return;
        
        const deleteForm = document.getElementById('deleteChannelForm');
        deleteForm.action = `/admin/notification-channels/${channelId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteChannelModal'));
        deleteModal.show();
    }

    // التحقق من صحة تنسيق JSON
    document.getElementById('config').addEventListener('blur', function() {
        validateJson(this);
    });
    
    document.getElementById('edit_config').addEventListener('blur', function() {
        validateJson(this);
    });
    
    function validateJson(element) {
        if (element.value.trim() === '') return;
        
        try {
            JSON.parse(element.value);
            element.classList.remove('is-invalid');
        } catch (e) {
            element.classList.add('is-invalid');
            alert('تنسيق JSON غير صحيح. الرجاء التحقق من الإدخال.');
        }
    }
</script>
@endsection
