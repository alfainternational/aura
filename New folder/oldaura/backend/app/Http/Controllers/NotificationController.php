<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات للمستخدم الحالي
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(15);
        $unreadCount = $user->unreadNotificationsCount();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    /**
     * تحديث حالة الإشعار إلى مقروء
     */
    public function markAsRead($id)
    {
        $notification = UserNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }
    
    /**
     * تحديث حالة جميع الإشعارات إلى مقروءة
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['read_at' => now()]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'تم تحديث جميع الإشعارات كمقروءة');
    }
    
    /**
     * حذف إشعار
     */
    public function destroy($id)
    {
        $notification = UserNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح');
    }
    
    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات بنجاح');
    }
    
    /**
     * الحصول على عدد الإشعارات غير المقروءة (لاستخدامها مع AJAX)
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotificationsCount();
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    /**
     * الحصول على آخر الإشعارات غير المقروءة (لاستخدامها مع AJAX)
     */
    public function getLatestUnread()
    {
        $notifications = Auth::user()->unreadNotifications()->take(5)->get();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }
}
