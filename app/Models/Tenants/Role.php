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

    /** @var string Administrador (wireframe default role) */
    public const ROLE_ADMIN = 'Administrador';

    /** @var string Docente / Teacher */
    public const ROLE_TEACHER = 'Docente';

    /** @var string Estudiante / Student */
    public const ROLE_STUDENT = 'Estudiante';

    /** @var string Padre/Tutor / Parent */
    public const ROLE_PARENT = 'Padre/Tutor';

    /** @var string Legacy generic user (editable, not locked) */
    public const ROLE_USER = 'Usuario';

    /** Roles that cannot be deleted or renamed (4 default + Super Admin) */
    protected static $lockedRoles = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_TEACHER,
        self::ROLE_STUDENT,
        self::ROLE_PARENT,
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getAclAttribute()
    {
        return [
            // 'show' => auth()->user()->can(self::P_READ),
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
