<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VoiceCall;
use App\Models\VoiceCallParticipant;

class CheckVoiceCallAccess
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
        $voiceCallId = $request->route('voiceCall') ?? $request->input('voice_call_id');
        
        if (!$voiceCallId) {
            abort(404, 'المكالمة غير موجودة');
        }
        
        // التحقق من وجود المكالمة
        $voiceCall = VoiceCall::find($voiceCallId);
        if (!$voiceCall) {
            abort(404, 'المكالمة غير موجودة');
        }
        
        // التحقق من أن المستخدم مشارك في المكالمة
        $participant = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$participant) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المكالمة');
        }
        
        // إضافة المكالمة والمشارك إلى الطلب
        $request->attributes->add(['voiceCall' => $voiceCall]);
        $request->attributes->add(['callParticipant' => $participant]);

        return $next($request);
    }
}
