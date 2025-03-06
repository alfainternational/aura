@extends('layouts.admin')

@section('title', 'إدارة قنوات الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">قنوات الإشعارات</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-channels.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> إضافة قناة جديدة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th>الوصف</th>
                                <th>افتراضي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($channels as $channel)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $channel->name }}</td>
                                    <td>
                                        @switch($channel->type)
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
                                                <span class="badge badge-secondary">{{ $channel->type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($channel->is_active)
                                            <span class="badge badge-success">مفعل</span>
                                        @else
                                            <span class="badge badge-danger">غير مفعل</span>
                                        @endif
                                    </td>
                                    <td>{{ $channel->description }}</td>
                                    <td>
                                        @if($channel->is_default)
                                            <span class="badge badge-warning">افتراضي</span>
                                        @else
                                            <form action="{{ route('admin.notification-channels.set-default', $channel->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-warning">
                                                    جعله افتراضي
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.notification-channels.show', $channel->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.notification-channels.edit', $channel->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.notification-channels.toggle-status', $channel->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $channel->is_active ? 'btn-danger' : 'btn-success' }}">
                                                    <i class="fas {{ $channel->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.notification-channels.destroy', $channel->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه القناة؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد قنوات إشعارات متاحة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
