<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTenantDomains extends Command
{
    protected $signature = 'tenants:sync-domains
                            {--dry-run : Show what would be done without making changes}';

    protected $description = 'Sync domains table from tenants. Fixes "Tenant could not be identified" when domains table was damaged or emptied.';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $baseDomain = config('tenancy.central_domains')[0] ?? 'escuelapresente.com';

        $tenants = Tenant::all();
        $fixed = 0;
        $skipped = 0;

        foreach ($tenants as $tenant) {
            $exists = DB::connection(config('tenancy.database.central_connection', config('database.default')))
                ->table('domains')
                ->where('tenant_id', $tenant->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $domain = $tenant->domain ?? $tenant->id . '.' . $baseDomain;
            $domain = strtolower(trim(preg_replace('#^https?://#i', '', preg_replace('#:\d+$#', '', $domain))));

            if ($dryRun) {
                $this->line("Would add: tenant_id={$tenant->id} -> domain={$domain}");
                $fixed++;
                continue;
            }

            try {
                $tenant->createDomain(['domain' => $domain]);
                $this->info("Added: {$tenant->id} -> {$domain}");
                $fixed++;
            } catch (\Throwable $e) {
                $this->error("Failed for {$tenant->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Done. Fixed: {$fixed}, Skipped (already had domain): {$skipped}");

        if ($fixed > 0 && ! $dryRun) {
            $this->line('Run: php artisan cache:clear');
        }

        return Command::SUCCESS;
    }
}
