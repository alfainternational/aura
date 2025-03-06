@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">إدارة المستخدمين</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">المستخدمين</li>
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
                        <h4 class="card-title flex-grow-1">قائمة المستخدمين</h4>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> إضافة مستخدم جديد
                            </a>
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
                            <select class="form-select" id="roleFilter">
                                <option value="">كل الأدوار</option>
                                <option value="customer">عميل</option>
                                <option value="merchant">تاجر</option>
                                <option value="agent">وكيل</option>
                                <option value="messenger">مندوب</option>
                                <option value="admin">مشرف</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">كل الحالات</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                                <option value="blocked">محظور</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="users-table" class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                            <label class="form-check-label" for="selectAllCheckbox"></label>
                                        </div>
                                    </th>
                                    <th>المعرف</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users ?? [] as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="user-{{ $user->id }}">
                                            <label class="form-check-label" for="user-{{ $user->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="" class="rounded-circle avatar-xs">
                                                @else
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($user->name ?? 'U', 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h5 class="font-size-14 mb-0">{{ $user->name }}</h5>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role == 'customer')
                                            <span class="badge bg-primary">عميل</span>
                                        @elseif($user->role == 'merchant')
                                            <span class="badge bg-success">تاجر</span>
                                        @elseif($user->role == 'agent')
                                            <span class="badge bg-warning">وكيل</span>
                                        @elseif($user->role == 'messenger')
                                            <span class="badge bg-info">مندوب</span>
                                        @elseif($user->role == 'admin')
                                            <span class="badge bg-danger">مشرف</span>
                                        @else
                                            <span class="badge bg-secondary">غير معروف</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'غير معروف' }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @elseif($user->is_blocked)
                                            <span class="badge bg-danger">محظور</span>
                                        @else
                                            <span class="badge bg-warning">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye me-2"></i>عرض</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}"><i class="fas fa-edit me-2"></i>تعديل</a></li>
                                                @if($user->is_active)
                                                    <li><a class="dropdown-item text-warning" href="#" onclick="toggleUserStatus({{ $user->id }}, 'deactivate')"><i class="fas fa-ban me-2"></i>تعطيل</a></li>
                                                @else
                                                    <li><a class="dropdown-item text-success" href="#" onclick="toggleUserStatus({{ $user->id }}, 'activate')"><i class="fas fa-check-circle me-2"></i>تفعيل</a></li>
                                                @endif
                                                @if($user->is_blocked)
                                                    <li><a class="dropdown-item text-success" href="#" onclick="toggleUserStatus({{ $user->id }}, 'unblock')"><i class="fas fa-unlock me-2"></i>إلغاء الحظر</a></li>
                                                @else
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="toggleUserStatus({{ $user->id }}, 'block')"><i class="fas fa-lock me-2"></i>حظر</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})"><i class="fas fa-trash-alt me-2"></i>حذف</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا يوجد مستخدمين</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">عرض {{ $users->firstItem() ?? 0 }} إلى {{ $users->lastItem() ?? 0 }} من {{ $users->total() ?? 0 }} مستخدم</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" action="">
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
    // تحديد/إلغاء تحديد كل الصفوف
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody .form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // تبديل حالة المستخدم (تفعيل/تعطيل/حظر/إلغاء الحظر)
    function toggleUserStatus(userId, action) {
        if (!userId) return;
        
        let url = '';
        let method = 'POST';
        let confirmMessage = '';
        
        switch(action) {
            case 'activate':
                url = `/admin/users/${userId}/activate`;
                confirmMessage = 'هل أنت متأكد من رغبتك في تفعيل هذا المستخدم؟';
                break;
            case 'deactivate':
                url = `/admin/users/${userId}/deactivate`;
                confirmMessage = 'هل أنت متأكد من رغبتك في تعطيل هذا المستخدم؟';
                break;
            case 'block':
                url = `/admin/users/${userId}/block`;
                confirmMessage = 'هل أنت متأكد من رغبتك في حظر هذا المستخدم؟';
                break;
            case 'unblock':
                url = `/admin/users/${userId}/unblock`;
                confirmMessage = 'هل أنت متأكد من رغبتك في إلغاء حظر هذا المستخدم؟';
                break;
            default:
                return;
        }
        
        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = method;
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

    // حذف مستخدم
    function deleteUser(userId) {
        if (!userId) return;
        
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/users/${userId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // تصفية المستخدمين
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterUsers();
    });

    document.getElementById('roleFilter').addEventListener('change', function() {
        filterUsers();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterUsers();
    });

    function filterUsers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        
        const rows = document.querySelectorAll('#users-table tbody tr');
        
        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const role = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(7) .badge').textContent.toLowerCase();
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleFilter || role.includes(roleFilter);
            const matchesStatus = !statusFilter || status.includes(statusFilter);
            
            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
