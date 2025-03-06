<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'position',
        'department',
        'office_address',
        'office_phone',
        'emergency_contact',
        'permissions',
        'last_active_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'json',
        'last_active_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * فحص إذا كان للمسؤول صلاحية محددة
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $permissions = json_decode($this->permissions, true) ?: [];
        return in_array($permission, $permissions);
    }

    /**
     * فحص إذا كان للمسؤول أي من الصلاحيات المحددة
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        $userPermissions = json_decode($this->permissions, true) ?: [];
        return count(array_intersect($userPermissions, $permissions)) > 0;
    }

    /**
     * إضافة صلاحية للمسؤول
     *
     * @param string $permission
     * @return bool
     */
    public function addPermission($permission)
    {
        $permissions = json_decode($this->permissions, true) ?: [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = json_encode($permissions);
            return $this->save();
        }
        return true;
    }

    /**
     * إزالة صلاحية من المسؤول
     *
     * @param string $permission
     * @return bool
     */
    public function removePermission($permission)
    {
        $permissions = json_decode($this->permissions, true) ?: [];
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->permissions = json_encode(array_values($permissions));
            return $this->save();
        }
        return true;
    }
}
