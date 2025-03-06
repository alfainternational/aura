<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserTypeService
{
    /**
     * الحصول على قائمة أنواع المستخدمين
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            'admin' => 'مسؤول النظام',
            'supervisor' => 'مشرف',
            'customer' => 'عميل',
            'merchant' => 'تاجر',
            'agent' => 'وكيل',
            'messenger' => 'مندوب',
            'user' => 'مستخدم عادي',
        ];
    }

    /**
     * الحصول على اسم نوع المستخدم
     *
     * @param string $type
     * @return string|null
     */
    public function getTypeName($type)
    {
        $types = $this->getTypes();
        return $types[$type] ?? null;
    }

    /**
     * التحقق من صلاحية نوع المستخدم
     *
     * @param string $type
     * @return bool
     */
    public function isValidType($type)
    {
        $types = $this->getTypes();
        return isset($types[$type]);
    }

    /**
     * الحصول على عدد المستخدمين لكل نوع
     *
     * @param bool $useCache استخدام التخزين المؤقت
     * @return array
     */
    public function getUserCountByType($useCache = true)
    {
        if ($useCache) {
            return Cache::remember('user_count_by_type', 3600, function () {
                return $this->calculateUserCountByType();
            });
        }

        return $this->calculateUserCountByType();
    }

    /**
     * حساب عدد المستخدمين لكل نوع
     *
     * @return array
     */
    protected function calculateUserCountByType()
    {
        $counts = [];
        $types = $this->getTypes();

        foreach (array_keys($types) as $type) {
            $counts[$type] = User::where('user_type', $type)->count();
        }

        return $counts;
    }

    /**
     * الحصول على الصلاحيات المتاحة لنوع مستخدم معين
     *
     * @param string $type
     * @return array
     */
    public function getPermissionsForType($type)
    {
        $permissions = [
            'admin' => [
                'user.manage', 'user.view', 'user.create', 'user.edit', 'user.delete',
                'role.manage', 'role.view', 'role.create', 'role.edit', 'role.delete',
                'permission.manage', 'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
                'notification.manage', 'notification.view', 'notification.create', 'notification.edit', 'notification.delete',
                'system.settings', 'system.logs', 'system.backup',
                'report.view', 'report.export',
                'messaging.manage', 'messaging.view',
                'voice-call.manage', 'voice-call.view',
            ],
            'supervisor' => [
                'user.view', 'user.edit',
                'role.view',
                'permission.view',
                'notification.view', 'notification.create',
                'report.view', 'report.export',
                'messaging.view',
                'voice-call.view',
            ],
            'customer' => [
                'profile.view', 'profile.edit',
                'notification.view',
                'messaging.use', 'voice-call.use',
            ],
            'merchant' => [
                'profile.view', 'profile.edit',
                'notification.view',
                'messaging.use', 'voice-call.use',
                'merchant.dashboard', 'merchant.products', 'merchant.orders',
            ],
            'agent' => [
                'profile.view', 'profile.edit',
                'notification.view',
                'messaging.use', 'voice-call.use',
                'agent.dashboard', 'agent.customers', 'agent.transactions',
            ],
            'messenger' => [
                'profile.view', 'profile.edit',
                'notification.view',
                'messaging.use', 'voice-call.use',
                'messenger.dashboard', 'messenger.deliveries',
            ],
            'user' => [
                'profile.view', 'profile.edit',
                'notification.view',
                'messaging.use', 'voice-call.use',
            ],
        ];

        return $permissions[$type] ?? [];
    }
}
