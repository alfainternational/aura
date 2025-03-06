@extends('layouts.dashboard')

@section('title', 'المحادثة')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/messaging.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/emoji-picker.css') }}">
@endsection

@section('content')
<div class="messaging-container">
    <!-- Conversation Header -->
    <div class="conversation-header">
        <div class="conversation-info">
            @if($conversation->is_group)
                <div class="group-avatar">
                    <img src="{{ $conversation->image ? asset('storage/' . $conversation->image) : asset('assets/images/default-group.png') }}" alt="{{ $conversation->name }}">
                </div>
                <div class="group-details">
                    <h3>{{ $conversation->name }}</h3>
                    <p>{{ $conversation->participants->count() }} عضو</p>
                </div>
            @else
                @php
                    $participant = $conversation->participants->where('user_id', '!=', auth()->id())->first();
                    $otherUser = $participant ? $participant->user : null;
                @endphp
                <div class="user-avatar">
                    <img src="{{ $otherUser && $otherUser->profile ? asset('storage/' . $otherUser->profile->avatar) : asset('assets/images/default-avatar.png') }}" alt="{{ $otherUser->name ?? 'مستخدم' }}">
                    <span class="status-indicator {{ $otherUser && $otherUser->isOnline() ? 'online' : 'offline' }}"></span>
                </div>
                <div class="user-details">
                    <h3>{{ $otherUser->name ?? 'مستخدم' }}</h3>
                    <p>{{ $otherUser && $otherUser->isOnline() ? 'متصل الآن' : 'غير متصل' }}</p>
                </div>
            @endif
        </div>
        <div class="conversation-actions">
            <button class="btn btn-voice-call" data-conversation-id="{{ $conversation->id }}" title="مكالمة صوتية">
                <i class="fas fa-phone-alt"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-more" data-toggle="dropdown" title="المزيد من الخيارات">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    @if($conversation->is_group)
                        <a class="dropdown-item view-group-info" href="javascript:void(0);">معلومات المجموعة</a>
                        @if($conversation->isAdmin(auth()->id()))
                            <a class="dropdown-item add-participants" href="javascript:void(0);">إضافة أعضاء</a>
                        @endif
                    @else
                        <a class="dropdown-item view-profile" href="{{ route('customer.profile.view', ['id' => $otherUser->id ?? 0]) }}">عرض الملف الشخصي</a>
                    @endif
                    <a class="dropdown-item search-messages" href="javascript:void(0);">بحث في الرسائل</a>
                    <a class="dropdown-item clear-messages" href="javascript:void(0);">مسح المحادثة</a>
                    <a class="dropdown-item {{ $conversation->isMuted() ? 'unmute-conversation' : 'mute-conversation' }}" href="javascript:void(0);">
                        {{ $conversation->isMuted() ? 'إلغاء كتم الإشعارات' : 'كتم الإشعارات' }}
                    </a>
                    <a class="dropdown-item block-conversation" href="javascript:void(0);">حظر</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="messages-container" id="messages-container">
        <div class="messages-list" id="messages-list">
            @if($messages->isEmpty())
                <div class="no-messages">
                    <div class="no-messages-icon">
                        <i class="far fa-comment-dots"></i>
                    </div>
                    <p>لا توجد رسائل بعد. ابدأ المحادثة الآن!</p>
                </div>
            @else
                <div class="messages-date-divider" id="messages-date-{{ $messages->first()->created_at->format('Y-m-d') }}">
                    <span>{{ $messages->first()->created_at->format('Y-m-d') }}</span>
                </div>
                
                @php
                    $currentDate = $messages->first()->created_at->format('Y-m-d');
                @endphp
                
                @foreach($messages as $message)
                    @php
                        $messageDate = $message->created_at->format('Y-m-d');
                    @endphp
                    
                    @if($currentDate != $messageDate)
                        <div class="messages-date-divider" id="messages-date-{{ $messageDate }}">
                            <span>{{ $messageDate }}</span>
                        </div>
                        @php
                            $currentDate = $messageDate;
                        @endphp
                    @endif
                    
                    <div class="message {{ $message->user_id == auth()->id() ? 'message-sent' : 'message-received' }}" 
                         id="message-{{ $message->id }}" 
                         data-message-id="{{ $message->id }}">
                        <div class="message-avatar">
                            <img src="{{ $message->user->profile ? asset('storage/' . $message->user->profile->avatar) : asset('assets/images/default-avatar.png') }}" 
                                 alt="{{ $message->user->name }}">
                        </div>
                        <div class="message-content">
                            @if($message->deleted_at)
                                <div class="message-deleted">
                                    <i class="fas fa-ban"></i> تم حذف هذه الرسالة
                                </div>
                            @else
                                @if($message->type == 'text')
                                    <div class="message-text">
                                        {!! nl2br(e($message->content)) !!}
                                    </div>
                                @elseif($message->type == 'image')
                                    <div class="message-image">
                                        <img src="{{ asset('storage/' . $message->content) }}" alt="صورة" class="img-fluid message-img">
                                    </div>
                                @elseif($message->type == 'voice')
                                    <div class="message-voice">
                                        <audio controls>
                                            <source src="{{ asset('storage/' . $message->content) }}" type="audio/mpeg">
                                            متصفحك لا يدعم مشغل الصوت.
                                        </audio>
                                    </div>
                                @elseif($message->type == 'file')
                                    <div class="message-file">
                                        <a href="{{ asset('storage/' . $message->content) }}" download>
                                            <i class="fas fa-file"></i> {{ basename($message->content) }}
                                        </a>
                                    </div>
                                @endif
                                
                                <div class="message-meta">
                                    <span class="message-time">{{ $message->created_at->format('h:i A') }}</span>
                                    @if($message->user_id == auth()->id())
                                        <span class="message-status">
                                            @if($message->status == 'sent')
                                                <i class="fas fa-check" title="تم الإرسال"></i>
                                            @elseif($message->status == 'delivered')
                                                <i class="fas fa-check-double" title="تم التسليم"></i>
                                            @elseif($message->status == 'read')
                                                <i class="fas fa-check-double text-primary" title="تم القراءة"></i>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        @if($message->user_id == auth()->id() && !$message->deleted_at)
                            <div class="message-actions dropdown">
                                <button class="btn btn-sm message-more" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item message-reply" href="javascript:void(0);" data-message-id="{{ $message->id }}">
                                        <i class="fas fa-reply"></i> رد
                                    </a>
                                    <a class="dropdown-item message-forward" href="javascript:void(0);" data-message-id="{{ $message->id }}">
                                        <i class="fas fa-share"></i> إعادة توجيه
                                    </a>
                                    <a class="dropdown-item message-delete" href="javascript:void(0);" data-message-id="{{ $message->id }}">
                                        <i class="fas fa-trash"></i> حذف
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Message Input Container -->
    <div class="message-input-container">
        <div class="reply-container" id="reply-container" style="display: none;">
            <div class="reply-content">
                <div class="reply-text" id="reply-text"></div>
                <div class="reply-user" id="reply-user"></div>
            </div>
            <button class="btn btn-sm btn-cancel-reply" id="cancel-reply">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="message-form" class="message-form">
            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
            <input type="hidden" name="reply_to" id="reply-to-id" value="">
            
            <div class="message-attachments">
                <button type="button" class="btn btn-attachment" id="btn-attachment">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="file-input" name="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                
                <button type="button" class="btn btn-emoji" id="btn-emoji">
                    <i class="far fa-smile"></i>
                </button>
            </div>
            
            <div class="message-input-wrapper">
                <textarea name="message" id="message-input" class="form-control message-input" placeholder="اكتب رسالة..." rows="1"></textarea>
            </div>
            
            <div class="message-send">
                <button type="button" class="btn btn-voice-record" id="btn-voice-record">
                    <i class="fas fa-microphone"></i>
                </button>
                <button type="submit" class="btn btn-send" id="btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
        
        <div class="voice-recording-container" id="voice-recording-container" style="display: none;">
            <div class="voice-recording-info">
                <i class="fas fa-microphone recording-icon"></i>
                <span class="recording-timer" id="recording-timer">00:00</span>
            </div>
            <div class="voice-recording-actions">
                <button class="btn btn-cancel-recording" id="btn-cancel-recording">
                    <i class="fas fa-times"></i> إلغاء
                </button>
                <button class="btn btn-send-recording" id="btn-send-recording">
                    <i class="fas fa-paper-plane"></i> إرسال
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Group Info Modal -->
<div class="modal fade" id="groupInfoModal" tabindex="-1" role="dialog" aria-labelledby="groupInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupInfoModalLabel">معلومات المجموعة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="group-info-content">
                    <!-- Will be populated via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emoji Picker -->
<div class="emoji-picker-container" id="emoji-picker-container" style="display: none;">
    <div class="emoji-picker" id="emoji-picker">
        <!-- Will be populated via JavaScript -->
    </div>
</div>

<!-- Voice Call Modal -->
<div class="modal fade" id="voiceCallModal" tabindex="-1" role="dialog" aria-labelledby="voiceCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="voice-call-avatar">
                    <img src="" alt="صورة المستخدم" id="voice-call-avatar">
                </div>
                <h5 class="voice-call-name" id="voice-call-name"></h5>
                <p class="voice-call-status" id="voice-call-status">جاري الاتصال...</p>
                <div class="voice-call-timer" id="voice-call-timer" style="display: none;">00:00</div>
                <div class="voice-call-actions">
                    <button class="btn btn-danger btn-circle btn-end-call" id="btn-end-call">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                    <button class="btn btn-success btn-circle btn-answer-call" id="btn-answer-call" style="display: none;">
                        <i class="fas fa-phone-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/emoji-picker.js') }}"></script>
<script src="{{ asset('assets/js/messaging.js') }}"></script>
<script>
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ auth()->id() }};
    const isGroup = {{ $conversation->is_group ? 'true' : 'false' }};
    
    // تحديث حالة الرسائل إلى "مقروءة"
    $(document).ready(function() {
        $.ajax({
            url: "{{ route('messaging.mark-as-read') }}",
            method: 'POST',
            data: {
                conversation_id: conversationId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('Messages marked as read');
            }
        });
        
        // تمرير المحادثة إلى أسفل
        scrollToBottom();
    });
    
    // تمرير المحادثة إلى أسفل
    function scrollToBottom() {
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // بدء مكالمة صوتية
    $('.btn-voice-call').on('click', function() {
        const recipientId = isGroup ? null : {{ $otherUser->id ?? 0 }};
        
        if (isGroup) {
            // إضافة منطق للمكالمات الجماعية هنا
        } else {
            // بدء مكالمة فردية
            initiateVoiceCall(recipientId);
        }
    });
    
    function initiateVoiceCall(recipientId) {
        $.ajax({
            url: "{{ route('voice-call.initiate') }}",
            method: 'POST',
            data: {
                recipient_id: recipientId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    $('#voice-call-avatar').attr('src', response.recipient_avatar);
                    $('#voice-call-name').text(response.recipient_name);
                    $('#voiceCallModal').modal('show');
                    
                    // بدء الاتصال مع Pusher أو أي تقنية للاتصال المباشر
                }
            }
        });
    }
</script>
@endsection