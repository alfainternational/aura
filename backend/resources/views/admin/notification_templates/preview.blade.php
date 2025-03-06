@extends('layouts.admin')

@section('title', 'معاينة قالب الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">معاينة قالب الإشعارات: {{ $template->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.notification-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.notification-templates.show', $template->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-info-circle"></i> عرض التفاصيل
                        </a>
                        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> هذه معاينة للقالب باستخدام بيانات تجريبية. قد يظهر القالب النهائي بشكل مختلف حسب البيانات الفعلية المستخدمة.
                    </div>
                    
                    <div class="preview-container">
                        @if($template->channel && $template->channel->type == 'email')
                            <div class="card email-preview mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">معاينة البريد الإلكتروني</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>الموضوع:</label>
                                        <input type="text" class="form-control" value="{{ $subject }}" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>المحتوى:</label>
                                        <div class="email-body p-3 bg-white border rounded">
                                            {!! nl2br(e($body)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($template->channel && $template->channel->type == 'sms')
                            <div class="card sms-preview mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">معاينة الرسالة النصية</h3>
                                </div>
                                <div class="card-body">
                                    <div class="sms-message p-3 bg-light border rounded" style="max-width: 300px; margin: 0 auto;">
                                        <div class="text-right">{{ $body }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($template->channel && $template->channel->type == 'telegram')
                            <div class="card telegram-preview mb-4">
                                <div class="card-header" style="background-color: #0088cc; color: white;">
                                    <h3 class="card-title">معاينة رسالة تيليجرام</h3>
                                </div>
                                <div class="card-body" style="background-color: #f5f5f5;">
                                    <div class="telegram-message p-3 bg-white border rounded" style="max-width: 500px; margin: 0 auto; border-radius: 10px;">
                                        <div class="text-right">{{ $body }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($template->channel && $template->channel->type == 'whatsapp')
                            <div class="card whatsapp-preview mb-4">
                                <div class="card-header" style="background-color: #25D366; color: white;">
                                    <h3 class="card-title">معاينة رسالة واتساب</h3>
                                </div>
                                <div class="card-body" style="background-color: #e5ddd5;">
                                    <div class="whatsapp-message p-3 bg-white border rounded" style="max-width: 500px; margin: 0 auto; border-radius: 10px;">
                                        <div class="text-right">{{ $body }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">معاينة القالب</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>الموضوع:</label>
                                        <input type="text" class="form-control" value="{{ $subject }}" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>المحتوى:</label>
                                        <div class="message-body p-3 bg-light border rounded">
                                            {!! nl2br(e($body)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>المتغيرات المستخدمة</h4>
                            <div>
                                @if(is_array($template->variables) && count($template->variables) > 0)
                                    @foreach($template->variables as $variable)
                                        <span class="badge badge-info mr-1 mb-1">{{ $variable }}</span>
                                    @endforeach
                                @else
                                    <p class="text-muted">لا توجد متغيرات مستخدمة</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.notification-templates.edit', $template->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> تعديل القالب
                    </a>
                    <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .email-body, .sms-message, .telegram-message, .whatsapp-message {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
    
    .email-preview {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .sms-message {
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.15);
    }
    
    .telegram-message, .whatsapp-message {
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
