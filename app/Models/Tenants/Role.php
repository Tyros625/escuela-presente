<?php

namespace App\Models\Tenants;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role as RoleBase;

class Role extends RoleBase
{
    use Notifiable;

    // Permissions for interact with model
    public const P_CREATE = 'create role';

    public const P_READ = 'read role';

    public const P_UPDATE = 'update role';

    public const P_DELETE = 'delete role';

    public const ROLE_SUPER_ADMIN = 'Super Admin';

    public const ROLE_ADMIN = 'Admin';

    public const ROLE_USER = 'Usuario';

    protected static $lockedRoles = [
        self::ROLE_SUPER_ADMIN,
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getAclAttribute()
    {
        return [
            //'show' => auth()->user()->can(self::P_READ),
            'update' => auth()->user()->can(self::P_UPDATE),
            'delete' => auth()->user()->can(self::P_DELETE),
        ];
    }

    public function getIsLockedAttribute()
    {
        return $this->isLocked();
    }

    public function isLocked()
    {
        return self::isLockedRole($this->name);
    }

    public static function isLockedRole($roleName)
    {
        return in_array($roleName, self::$lockedRoles);
    }
}
