<?php

namespace App\Models\Tenants;

use App\Builders\UserBuilder;
use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, HasApiTokens;

    public const P_CREATE = 'create user';

    public const P_READ = 'read user';

    public const P_UPDATE = 'update user';

    public const P_DELETE = 'delete user';

    protected $guard_name = 'api';

    protected $fillable = [
        'name', 'email',
        'password', 'active',
        'student_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    protected $appends = [
        'is_super_admin',
    ];

    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            return $this->attributes['password'] = Hash::make($value);
        }

        $this->attributes['password'] = $value;
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->hasRole(Role::ROLE_SUPER_ADMIN);
    }

    public function getActiveAttribute()
    {
        return $this->attributes['active'] ? true : false;
    }

    public function getRoleAttribute()
    {
        return optional($this->roles()->first())->name;
    }

    public function accountConfiguration()
    {
        return $this->hasOne(AccountConfiguration::class);
    }

    public function canJoinGroup(int $groupId): bool
    {
        return $this->group_id == $groupId;
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public static function query(): UserBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
