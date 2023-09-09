<?php

namespace App\Providers;

use App\Models\Tenants\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->hasRole(Role::ROLE_SUPER_ADMIN) ? true : null;
        });
    }
}
