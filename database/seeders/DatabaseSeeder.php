<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@'.config('tenancy.central_domains')[0],
            'password' => '123456',
        ]);

        //$this->createTenant();
    }

    protected function createTenant()
    {
        $domain = 'demo.'.config('tenancy.central_domains')[0];

        $tenant = Tenant::create([
            'id' => 'demo',
            'school_name' => 'demo',
            'domain' => $domain,
            'name' => 'Demo',
            'email' => 'demo@'.config('tenancy.central_domains')[0],
            'password' => '123456',
        ]);

        $tenant->createDomain(['domain' => $domain]);

        Artisan::call('tenants:seed');
    }
}
