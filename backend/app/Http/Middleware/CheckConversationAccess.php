<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\ConversationParticipant;

class CheckConversationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $conversationId = $request->route('conversation') ?? $request->input('conversation_id');
        
        if (!$conversationId) {
            abort(404, 'المحادثة غير موجودة');
        }
        
        // التحقق من وجود المحادثة
        $conversation = Conversation::find($conversationId);
        if (!$conversation) {
            abort(404, 'المحادثة غير موجودة');
        }
        
        // التحقق من أن المستخدم مشارك في المحادثة
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();
            
        if (!$participant) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المحادثة');
        }
        
        // إضافة المحادثة والمشارك إلى الطلب
        $request->attributes->add(['conversation' => $conversation]);
        $request->attributes->add(['participant' => $participant]);

        return $next($request);
    }
}
