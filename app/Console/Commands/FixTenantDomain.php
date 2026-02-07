<?php

namespace App\Console\Commands;

use App\Http\Controllers\TenantController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixTenantDomain extends Command
{
    protected $signature = 'tenants:fix-domain
                            {tenant_id=secundaria87 : Tenant ID (e.g. secundaria87)}
                            {--domain= : Set domain to this hostname (e.g. secundaria87.localhost for local)}';

    protected $description = 'Normalize domain in domains table to hostname only (strip scheme and port)';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $forceDomain = $this->option('domain');

        $row = DB::table('domains')->where('tenant_id', $tenantId)->first();

        if (! $row) {
            $this->error("No domain found for tenant_id '{$tenantId}'. Register the customer in the admin panel first.");

            return Command::FAILURE;
        }

        $oldDomain = $row->domain;
        $newDomain = $forceDomain
            ? TenantController::normalizeDomainForTenancy($forceDomain)
            : TenantController::normalizeDomainForTenancy($oldDomain);

        if ($oldDomain === $newDomain && ! $forceDomain) {
            $this->info("Already in correct format: {$oldDomain}");
            $this->line('To use locally, run: php artisan tenants:fix-domain secundaria87 --domain=secundaria87.localhost');

            return Command::SUCCESS;
        }

        DB::table('domains')->where('tenant_id', $tenantId)->update(['domain' => $newDomain]);

        $this->info("Updated:");
        $this->line("  Before: {$oldDomain}");
        $this->line("  After:  {$newDomain}");
        $this->newLine();
        $this->info("You can now open http://{$newDomain}:8000");

        return Command::SUCCESS;
    }
}
