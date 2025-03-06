@extends('layouts.admin')

@section('title', 'تعديل قناة إشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">تعديل قناة إشعارات: {{ $notificationChannel->name }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.notification-channels.update', $notificationChannel->id) }}">
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
                            <label for="name">اسم القناة</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $notificationChannel->name) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">نوع القناة</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">-- اختر نوع القناة --</option>
                                <option value="email" {{ old('type', $notificationChannel->type) == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                                <option value="sms" {{ old('type', $notificationChannel->type) == 'sms' ? 'selected' : '' }}>رسائل نصية</option>
                                <option value="telegram" {{ old('type', $notificationChannel->type) == 'telegram' ? 'selected' : '' }}>تيليجرام</option>
                                <option value="whatsapp" {{ old('type', $notificationChannel->type) == 'whatsapp' ? 'selected' : '' }}>واتساب</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف القناة</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $notificationChannel->description) }}</textarea>
                        </div>
                        
                        @php
                            $settings = $notificationChannel->settings ?? [];
                        @endphp
                        
                        <div class="form-group" id="settings-email" style="display: none;">
                            <h4>إعدادات البريد الإلكتروني</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Host</label>
                                        <input type="text" class="form-control" name="settings[smtp_host]" value="{{ old('settings.smtp_host', $settings['smtp_host'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Port</label>
                                        <input type="text" class="form-control" name="settings[smtp_port]" value="{{ old('settings.smtp_port', $settings['smtp_port'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Username</label>
                                        <input type="text" class="form-control" name="settings[smtp_username]" value="{{ old('settings.smtp_username', $settings['smtp_username'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Password</label>
                                        <input type="password" class="form-control" name="settings[smtp_password]" value="{{ old('settings.smtp_password', $settings['smtp_password'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Encryption</label>
                                        <select class="form-control" name="settings[smtp_encryption]">
                                            <option value="">None</option>
                                            <option value="tls" {{ old('settings.smtp_encryption', $settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('settings.smtp_encryption', $settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Address</label>
                                        <input type="email" class="form-control" name="settings[from_address]" value="{{ old('settings.from_address', $settings['from_address'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Name</label>
                                        <input type="text" class="form-control" name="settings[from_name]" value="{{ old('settings.from_name', $settings['from_name'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" id="settings-sms" style="display: none;">
                            <h4>إعدادات الرسائل النصية</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>مزود الخدمة</label>
                                        <select class="form-control" name="settings[sms_provider]">
                                            <option value="twilio" {{ old('settings.sms_provider', $settings['sms_provider'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                            <option value="nexmo" {{ old('settings.sms_provider', $settings['sms_provider'] ?? '') == 'nexmo' ? 'selected' : '' }}>Nexmo (Vonage)</option>
                                            <option value="messagebird" {{ old('settings.sms_provider', $settings['sms_provider'] ?? '') == 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Account ID / SID</label>
                                        <input type="text" class="form-control" name="settings[account_id]" value="{{ old('settings.account_id', $settings['account_id'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Auth Token / API Key</label>
                                        <input type="password" class="form-control" name="settings[auth_token]" value="{{ old('settings.auth_token', $settings['auth_token'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>رقم المرسل</label>
                                        <input type="text" class="form-control" name="settings[from_number]" value="{{ old('settings.from_number', $settings['from_number'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" id="settings-telegram" style="display: none;">
                            <h4>إعدادات تيليجرام</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bot Token</label>
                                        <input type="text" class="form-control" name="settings[bot_token]" value="{{ old('settings.bot_token', $settings['bot_token'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Chat ID (اختياري - للإرسال لمجموعة)</label>
                                        <input type="text" class="form-control" name="settings[chat_id]" value="{{ old('settings.chat_id', $settings['chat_id'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" id="settings-whatsapp" style="display: none;">
                            <h4>إعدادات واتساب</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>مزود الخدمة</label>
                                        <select class="form-control" name="settings[whatsapp_provider]">
                                            <option value="twilio" {{ old('settings.whatsapp_provider', $settings['whatsapp_provider'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                            <option value="messagebird" {{ old('settings.whatsapp_provider', $settings['whatsapp_provider'] ?? '') == 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                                            <option value="whatsapp_business" {{ old('settings.whatsapp_provider', $settings['whatsapp_provider'] ?? '') == 'whatsapp_business' ? 'selected' : '' }}>WhatsApp Business API</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Account ID / SID</label>
                                        <input type="text" class="form-control" name="settings[whatsapp_account_id]" value="{{ old('settings.whatsapp_account_id', $settings['whatsapp_account_id'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Auth Token / API Key</label>
                                        <input type="password" class="form-control" name="settings[whatsapp_auth_token]" value="{{ old('settings.whatsapp_auth_token', $settings['whatsapp_auth_token'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>رقم المرسل</label>
                                        <input type="text" class="form-control" name="settings[whatsapp_from_number]" value="{{ old('settings.whatsapp_from_number', $settings['whatsapp_from_number'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $notificationChannel->is_active) ? 'checked' : '' }} value="1">
                                <label class="custom-control-label" for="is_active">تفعيل القناة</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" {{ old('is_default', $notificationChannel->is_default) ? 'checked' : '' }} value="1">
                                <label class="custom-control-label" for="is_default">جعلها قناة افتراضية</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">تحديث</button>
                        <a href="{{ route('admin.notification-channels.index') }}" class="btn btn-secondary">إلغاء</a>
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
        // Show/hide settings based on channel type
        $('#type').change(function() {
            $('.form-group[id^="settings-"]').hide();
            if ($(this).val()) {
                $('#settings-' + $(this).val()).show();
            }
        });
        
        // Trigger change on page load
        $('#type').trigger('change');
    });
</script>
@endsection
