@extends('layouts.admin')

@section('title', 'تعديل قالب إشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">تعديل قالب إشعارات: {{ $notificationTemplate->name }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.notification-templates.update', $notificationTemplate->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="notification_channel_id">قناة الإشعارات</label>
                            <select class="form-control" id="notification_channel_id" name="notification_channel_id" required>
                                <option value="">-- اختر قناة الإشعارات --</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('notification_channel_id', $notificationTemplate->notification_channel_id) == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }} ({{ $channel->type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">اسم القالب</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $notificationTemplate->name) }}" required>
                            <small class="form-text text-muted">مثال: "إشعار تسجيل حساب جديد"، "إشعار إيداع ناجح"</small>
                        </div>
                        
                        <div id="subject-field" class="form-group">
                            <label for="subject">الموضوع</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $notificationTemplate->subject) }}">
                            <small class="form-text text-muted">مطلوب فقط للبريد الإلكتروني. يمكنك استخدام المتغيرات مثل {{user_name}}.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="body">محتوى الإشعار</label>
                            <textarea class="form-control" id="body" name="body" rows="6" required>{{ old('body', $notificationTemplate->body) }}</textarea>
                            <small class="form-text text-muted">يمكنك استخدام المتغيرات بالصيغة {{variable_name}}.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>المتغيرات المتاحة</label>
                            <div class="variables-container">
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('user_name')">{{user_name}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('title')">{{title}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('message')">{{message}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('action_url')">{{action_url}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('app_name')">{{app_name}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('amount')">{{amount}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('date')">{{date}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('time')">{{time}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('transaction_id')">{{transaction_id}}</button>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1 mb-1" onclick="insertVariable('status')">{{status}}</button>
                            </div>
                            <small class="form-text text-muted">اضغط على المتغير لإضافته في الموضع الحالي للمؤشر.</small>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $notificationTemplate->is_active) ? 'checked' : '' }} value="1">
                                <label class="custom-control-label" for="is_active">تفعيل القالب</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>المتغيرات المستخدمة في هذا القالب:</label>
                            <div>
                                @if(is_array($notificationTemplate->variables) && count($notificationTemplate->variables) > 0)
                                    @foreach($notificationTemplate->variables as $variable)
                                        <span class="badge badge-info mr-1">{{ $variable }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">لا توجد متغيرات مستخدمة</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">تحديث</button>
                        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-secondary">إلغاء</a>
                        <a href="{{ route('admin.notification-templates.preview', $notificationTemplate->id) }}" class="btn btn-info float-right" target="_blank">
                            <i class="fas fa-eye"></i> معاينة القالب
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show/hide subject field based on channel type
        $('#notification_channel_id').change(function() {
            var channelId = $(this).val();
            if (channelId) {
                var channelType = $('option:selected', this).text().match(/\((.*?)\)/)[1];
                if (channelType === 'email') {
                    $('#subject-field').show();
                } else {
                    $('#subject-field').hide();
                }
            } else {
                $('#subject-field').show();
            }
        });
        
        // Trigger change on page load
        $('#notification_channel_id').trigger('change');
    });
    
    // Function to insert variable at cursor position
    function insertVariable(variable) {
        var activeElement = document.activeElement;
        if (activeElement.id === 'subject' || activeElement.id === 'body') {
            var startPos = activeElement.selectionStart;
            var endPos = activeElement.selectionEnd;
            var text = activeElement.value;
            var variableText = '{{' + variable + '}}';
            
            activeElement.value = text.substring(0, startPos) + variableText + text.substring(endPos);
            activeElement.setSelectionRange(startPos + variableText.length, startPos + variableText.length);
            activeElement.focus();
        } else {
            // If no field is focused, add to body by default
            var bodyField = document.getElementById('body');
            var currentBody = bodyField.value;
            var variableText = '{{' + variable + '}}';
            
            bodyField.value = currentBody + (currentBody ? ' ' : '') + variableText;
        }
    }
</script>
@endsection
