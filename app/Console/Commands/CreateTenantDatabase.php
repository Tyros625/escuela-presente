<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Events\DatabaseCreated;
use Stancl\Tenancy\Events\CreatingDatabase;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;

class CreateTenantDatabase extends Command
{
    protected $signature = 'tenants:create-database
                            {tenant_id : Tenant ID (e.g. secundaria87)}';

    protected $description = 'Create tenant database and run migrations (fix TenantDatabaseDoesNotExistException)';

    public function handle(DatabaseManager $databaseManager): int
    {
        $tenantId = $this->argument('tenant_id');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant '{$tenantId}' not found in central database.");

            return Command::FAILURE;
        }

        $dbName = $tenant->database()->getName();

        $this->info("Tenant: {$tenantId}");
        $this->line("Database: {$dbName}");
        $this->newLine();

        try {
            $tenant->database()->makeCredentials();
            $databaseManager->ensureTenantCanBeCreated($tenant);
        } catch (TenantDatabaseAlreadyExistsException $e) {
            $this->warn("Database '{$dbName}' already exists. Running migrations only.");
            $this->runMigrations($tenantId);

            return Command::SUCCESS;
        }

        event(new CreatingDatabase($tenant));
        $tenant->database()->manager()->createDatabase($tenant);
        event(new DatabaseCreated($tenant));

        $this->info("Database '{$dbName}' created.");
        $this->runMigrations($tenantId);

        return Command::SUCCESS;
    }

    protected function runMigrations(string $tenantId): void
    {
        $this->info('Running migrations...');
        $this->call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--force'  => true,
        ]);
        $this->info('Done. You can now open the tenant URL.');
    }
}
