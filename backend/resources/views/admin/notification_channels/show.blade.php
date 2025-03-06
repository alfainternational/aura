@extends('layouts.admin')

@section('title', 'عرض تفاصيل قناة الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل قناة الإشعارات: {{ $notificationChannel->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-channels.edit', $notificationChannel->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.notification-channels.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>اسم القناة:</label>
                                <p class="form-control-static">{{ $notificationChannel->name }}</p>
                            </div>
                            <div class="form-group">
                                <label>نوع القناة:</label>
                                <p class="form-control-static">
                                    @switch($notificationChannel->type)
                                        @case('email')
                                            <span class="badge badge-info">بريد إلكتروني</span>
                                            @break
                                        @case('sms')
                                            <span class="badge badge-primary">رسائل نصية</span>
                                            @break
                                        @case('telegram')
                                            <span class="badge badge-primary" style="background-color: #0088cc">تيليجرام</span>
                                            @break
                                        @case('whatsapp')
                                            <span class="badge badge-success">واتساب</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $notificationChannel->type }}</span>
                                    @endswitch
                                </p>
                            </div>
                            <div class="form-group">
                                <label>الحالة:</label>
                                <p class="form-control-static">
                                    @if($notificationChannel->is_active)
                                        <span class="badge badge-success">مفعل</span>
                                    @else
                                        <span class="badge badge-danger">غير مفعل</span>
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label>القناة الافتراضية:</label>
                                <p class="form-control-static">
                                    @if($notificationChannel->is_default)
                                        <span class="badge badge-warning">نعم</span>
                                    @else
                                        <span class="badge badge-secondary">لا</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ الإنشاء:</label>
                                <p class="form-control-static">{{ $notificationChannel->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div class="form-group">
                                <label>آخر تحديث:</label>
                                <p class="form-control-static">{{ $notificationChannel->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div class="form-group">
                                <label>الوصف:</label>
                                <p class="form-control-static">{{ $notificationChannel->description ?? 'لا يوجد وصف' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>إعدادات القناة</h4>
                            @php
                                $settings = $notificationChannel->settings ?? [];
                            @endphp
                            
                            @if($notificationChannel->type == 'email')
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>SMTP Host</th>
                                            <td>{{ $settings['smtp_host'] ?? 'غير محدد' }}</td>
                                            <th>SMTP Port</th>
                                            <td>{{ $settings['smtp_port'] ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>SMTP Username</th>
                                            <td>{{ $settings['smtp_username'] ?? 'غير محدد' }}</td>
                                            <th>SMTP Password</th>
                                            <td>******</td>
                                        </tr>
                                        <tr>
                                            <th>SMTP Encryption</th>
                                            <td>{{ $settings['smtp_encryption'] ?? 'غير محدد' }}</td>
                                            <th>From Address</th>
                                            <td>{{ $settings['from_address'] ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>From Name</th>
                                            <td colspan="3">{{ $settings['from_name'] ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @elseif($notificationChannel->type == 'sms')
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>مزود الخدمة</th>
                                            <td>{{ $settings['sms_provider'] ?? 'غير محدد' }}</td>
                                            <th>Account ID / SID</th>
                                            <td>{{ $settings['account_id'] ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Auth Token / API Key</th>
                                            <td>******</td>
                                            <th>رقم المرسل</th>
                                            <td>{{ $settings['from_number'] ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @elseif($notificationChannel->type == 'telegram')
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Bot Token</th>
                                            <td>{{ substr($settings['bot_token'] ?? '', 0, 10) . '...' }}</td>
                                            <th>Chat ID</th>
                                            <td>{{ $settings['chat_id'] ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @elseif($notificationChannel->type == 'whatsapp')
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>مزود الخدمة</th>
                                            <td>{{ $settings['whatsapp_provider'] ?? 'غير محدد' }}</td>
                                            <th>Account ID / SID</th>
                                            <td>{{ $settings['whatsapp_account_id'] ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Auth Token / API Key</th>
                                            <td>******</td>
                                            <th>رقم المرسل</th>
                                            <td>{{ $settings['whatsapp_from_number'] ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    لا توجد إعدادات متاحة لهذا النوع من القنوات أو الإعدادات غير محددة.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>قوالب الإشعارات المرتبطة</h4>
                            @if($notificationChannel->templates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>اسم القالب</th>
                                                <th>Slug</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notificationChannel->templates as $template)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $template->name }}</td>
                                                    <td>{{ $template->slug }}</td>
                                                    <td>
                                                        @if($template->is_active)
                                                            <span class="badge badge-success">مفعل</span>
                                                        @else
                                                            <span class="badge badge-danger">غير مفعل</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.notification-templates.show', $template->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    لا توجد قوالب إشعارات مرتبطة بهذه القناة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group">
                        <form action="{{ route('admin.notification-channels.toggle-status', $notificationChannel->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $notificationChannel->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas {{ $notificationChannel->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                {{ $notificationChannel->is_active ? 'تعطيل القناة' : 'تفعيل القناة' }}
                            </button>
                        </form>
                        
                        @if(!$notificationChannel->is_default)
                            <form action="{{ route('admin.notification-channels.set-default', $notificationChannel->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-star"></i> جعلها القناة الافتراضية
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.notification-channels.destroy', $notificationChannel->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه القناة؟')">
                                <i class="fas fa-trash"></i> حذف القناة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
