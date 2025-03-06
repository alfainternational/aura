@extends('layouts.admin')

@section('title', 'إدارة قوالب الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">قوالب الإشعارات</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-templates.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> إضافة قالب جديد
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
                                <th>اسم القالب</th>
                                <th>Slug</th>
                                <th>قناة الإشعارات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->slug }}</td>
                                    <td>
                                        @if($template->channel)
                                            <span class="badge 
                                                @switch($template->channel->type)
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
                                                {{ $template->channel->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger">لا توجد قناة</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge badge-success">مفعل</span>
                                        @else
                                            <span class="badge badge-danger">غير مفعل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.notification-templates.preview', $template->id) }}" class="btn btn-sm btn-default" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.notification-templates.show', $template->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <a href="{{ route('admin.notification-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.notification-templates.toggle-status', $template->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $template->is_active ? 'btn-danger' : 'btn-success' }}">
                                                    <i class="fas {{ $template->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.notification-templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا القالب؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد قوالب إشعارات متاحة</td>
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
