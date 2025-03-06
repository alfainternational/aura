@extends('layouts.admin')

@section('title', 'عرض تفاصيل قالب الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل قالب الإشعارات: {{ $notificationTemplate->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-templates.edit', $notificationTemplate->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>اسم القالب:</label>
                                <p class="form-control-static">{{ $notificationTemplate->name }}</p>
                            </div>
                            <div class="form-group">
                                <label>Slug:</label>
                                <p class="form-control-static">{{ $notificationTemplate->slug }}</p>
                            </div>
                            <div class="form-group">
                                <label>الحالة:</label>
                                <p class="form-control-static">
                                    @if($notificationTemplate->is_active)
                                        <span class="badge badge-success">مفعل</span>
                                    @else
                                        <span class="badge badge-danger">غير مفعل</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>قناة الإشعارات:</label>
                                <p class="form-control-static">
                                    @if($notificationTemplate->channel)
                                        <a href="{{ route('admin.notification-channels.show', $notificationTemplate->channel->id) }}">
                                            <span class="badge 
                                                @switch($notificationTemplate->channel->type)
                                                    @case('email')
                                                        badge-info
                                                        @break
                                                    @case('sms')
                                                        badge-primary
                                                        @break
                                                    @case('telegram')
                                                        badge-primary" style="background-color: #0088cc
                                                        @break
                                                    @case('whatsapp')
                                                        badge-success
                                                        @break
                                                    @default
                                                        badge-secondary
                                                @endswitch
                                            ">
                                                {{ $notificationTemplate->channel->name }} ({{ $notificationTemplate->channel->type }})
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge badge-danger">لا توجد قناة</span>
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label>تاريخ الإنشاء:</label>
                                <p class="form-control-static">{{ $notificationTemplate->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div class="form-group">
                                <label>آخر تحديث:</label>
                                <p class="form-control-static">{{ $notificationTemplate->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>المتغيرات المستخدمة</h4>
                            <div>
                                @if(is_array($notificationTemplate->variables) && count($notificationTemplate->variables) > 0)
                                    @foreach($notificationTemplate->variables as $variable)
                                        <span class="badge badge-info mr-1 mb-1">{{ $variable }}</span>
                                    @endforeach
                                @else
                                    <p class="text-muted">لا توجد متغيرات مستخدمة</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>محتوى القالب</h4>
                            @if($notificationTemplate->channel && $notificationTemplate->channel->type == 'email')
                                <div class="form-group">
                                    <label>الموضوع:</label>
                                    <div class="p-2 bg-light border rounded">{{ $notificationTemplate->subject ?: 'لا يوجد موضوع' }}</div>
                                </div>
                            @endif
                            
                            <div class="form-group">
                                <label>المحتوى:</label>
                                <div class="p-3 bg-light border rounded">
                                    {!! nl2br(e($notificationTemplate->body)) !!}
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.notification-templates.preview', $notificationTemplate->id) }}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-eye"></i> معاينة القالب مع بيانات تجريبية
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group">
                        <form action="{{ route('admin.notification-templates.toggle-status', $notificationTemplate->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $notificationTemplate->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas {{ $notificationTemplate->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                {{ $notificationTemplate->is_active ? 'تعطيل القالب' : 'تفعيل القالب' }}
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.notification-templates.destroy', $notificationTemplate->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا القالب؟')">
                                <i class="fas fa-trash"></i> حذف القالب
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
