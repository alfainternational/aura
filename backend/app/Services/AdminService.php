<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /**
     * Get user statistics
     */
    public function getUserStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'verified_users' => User::where('verification_status', 'verified')->count(),
            'user_types' => User::groupBy('user_type')
                ->select('user_type', DB::raw('count(*) as count'))
                ->get()
        ];
    }

    /**
     * Get paginated user list with filtering
     */
    public function getUserList(array $filters = [], int $perPage = 20)
    {
        $query = User::query();

        // Apply filters
        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (!empty($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Optional search
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('email', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('phone', 'LIKE', "%{$filters['search']}%");
            });
        }

        // Order and paginate
        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }

    /**
     * Update user status or type by admin
     */
    public function updateUserStatus(User $user, array $data)
    {
        $updatableFields = [
            'status', 
            'user_type', 
            'verification_status'
        ];

        $updateData = collect($data)
            ->only($updatableFields)
            ->filter()
            ->toArray();

        $user->fill($updateData);
        $user->save();

        Log::info('User status updated by admin', [
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
            'updated_fields' => array_keys($updateData)
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تحديث حالة المستخدم بنجاح',
            'user' => $user
        ];
    }

    /**
     * Manage supported countries and cities
     */
    public function manageSupportedLocations(array $locations)
    {
        // This would typically interact with a configuration or database table
        // For now, we'll use a service method
        $locationService = app(LocationService::class);

        Log::info('Supported locations updated', [
            'admin_id' => auth()->id(),
            'locations' => $locations
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تحديث المواقع المدعومة',
            'locations' => $locations
        ];
    }

    /**
     * Generate system-wide reports
     */
    public function generateSystemReport(array $options = [])
    {
        $report = [
            'user_statistics' => $this->getUserStatistics(),
            'recent_verifications' => $this->getRecentVerifications(),
            'system_health' => $this->checkSystemHealth()
        ];

        Log::info('System report generated', [
            'admin_id' => auth()->id(),
            'report_type' => $options['type'] ?? 'default'
        ]);

        return $report;
    }

    /**
     * Get recent identity verifications
     */
    private function getRecentVerifications()
    {
        return DB::table('identity_verifications')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Basic system health check
     */
    private function checkSystemHealth()
    {
        return [
            'database_connection' => $this->checkDatabaseConnection(),
            'storage_space' => $this->checkStorageSpace(),
            'pending_jobs' => $this->countPendingJobs()
        ];
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check available storage space
     */
    private function checkStorageSpace()
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        
        return [
            'free_space_bytes' => $free,
            'total_space_bytes' => $total,
            'free_percentage' => ($free / $total) * 100
        ];
    }

    /**
     * Count pending background jobs
     */
    private function countPendingJobs()
    {
        return DB::table('jobs')->count();
    }
}
