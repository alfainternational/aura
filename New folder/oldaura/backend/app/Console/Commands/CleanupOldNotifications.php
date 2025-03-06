<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserNotification;
use Carbon\Carbon;

class CleanupOldNotifications extends Command
{
    /**
     * اسم الأمر وتوصيفه
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : عدد الأيام للاحتفاظ بالإشعارات}';

    /**
     * وصف الأمر
     *
     * @var string
     */
    protected $description = 'حذف الإشعارات القديمة من قاعدة البيانات';

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $days = $this->option('days');
        $date = Carbon::now()->subDays($days);
        
        $this->info("جاري حذف الإشعارات الأقدم من {$days} يوم...");
        
        $count = UserNotification::where('created_at', '<', $date)->delete();
        
        $this->info("تم حذف {$count} إشعار بنجاح.");
        
        return Command::SUCCESS;
    }
}
