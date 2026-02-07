<?php

namespace App\Console\Commands;

use App\Jobs\CreateTenantAdmin;
use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTenantAdminUser extends Command
{
    protected $signature = 'tenants:create-admin
                            {tenant_id : Tenant ID (e.g. secundaria87)}';

    protected $description = 'Seed tenant DB and create admin user (email/password from central tenant) so you can log in';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant '{$tenantId}' not found.");

            return Command::FAILURE;
        }

        $this->info("Tenant: {$tenantId} ({$tenant->email})");
        $this->newLine();

        $this->info('Seeding tenant database (roles, permissions, etc.)...');
        tenancy()->initialize($tenant);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\TenantsSeeder', '--force' => true]);
        tenancy()->end();

        tenancy()->initialize($tenant);
        $userExists = \App\Models\Tenants\User::where('email', $tenant->email)->exists();
        tenancy()->end();

        if ($userExists) {
            $this->warn('Admin user already exists for this email. Use your password to log in.');
        } else {
            $this->info('Creating admin user...');
            (new CreateTenantAdmin($tenant))->handle();
        }

        $this->newLine();
        $this->info('Log in with:');
        $this->line('  Email:    ' . $tenant->email);
        $this->line('  Password: (the one you set when adding this school in the admin panel)');

        return Command::SUCCESS;
    }
}
