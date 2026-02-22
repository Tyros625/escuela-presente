<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ReflectionClass;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_SUPER_ADMIN)->exists()) {
            Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_SUPER_ADMIN,
                'guard_name' => 'api',
            ]);
        }

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_ADMIN)->exists()) {
            $adminRole = Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_ADMIN,
                'guard_name' => 'api',
            ]);
        } else {
            $adminRole = Role::where('name', \App\Models\Tenants\Role::ROLE_ADMIN)->first();
        }

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_TEACHER)->exists()) {
            Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_TEACHER,
                'guard_name' => 'api',
            ]);
        }

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_STUDENT)->exists()) {
            Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_STUDENT,
                'guard_name' => 'api',
            ]);
        }

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_PARENT)->exists()) {
            Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_PARENT,
                'guard_name' => 'api',
            ]);
        }

        if (! Role::where('name', \App\Models\Tenants\Role::ROLE_USER)->exists()) {
            Role::create([
                'name' => \App\Models\Tenants\Role::ROLE_USER,
                'guard_name' => 'api',
            ]);
        }

        $this->registerPermissions(\App\Models\Tenants\Assist::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Balances::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Dashboard::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Dinner::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\GeneralConfiguration::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Incident::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\IncidentReport::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Role::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Specialty::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Student::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\Teacher::class, [$adminRole]);
        $this->registerPermissions(\App\Models\Tenants\User::class, [$adminRole]);
    }

    protected function registerPermissions($model, array $roles)
    {
        $reflection = new ReflectionClass($model);
        $constants = $reflection->getConstants();

        foreach ($constants as $constant => $value) {
            if (strpos($constant, 'P_') !== false) {
                if (! Permission::where('name', $value)->exists()) {
                    Permission::create([
                        'name' => $value,
                        'guard_name' => 'api',
                    ]);
                }

                foreach ($roles as $role) {
                    $role->givePermissionTo($value);
                }
            }
        }
    }
}
