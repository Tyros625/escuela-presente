<?php
/**
 * Tenant Active Column Diagnostic Script
 * Run: php test-tenant-active.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

echo "=== Tenant Active Column Diagnostic ===\n\n";

// 1. Check central connection config
$centralConn = config('tenancy.database.central_connection', config('database.default'));
echo "1. Central connection: {$centralConn}\n";
echo "   Default connection: " . config('database.default') . "\n";
echo "   DB_CONNECTION from env: " . env('DB_CONNECTION') . "\n\n";

// 2. Check if active column exists
$hasColumn = Schema::hasColumn('tenants', 'active');
echo "2. tenants.active column exists: " . ($hasColumn ? 'YES ✓' : 'NO ✗') . "\n\n";

if (!$hasColumn) {
    echo "⚠️  ERROR: Column doesn't exist. Run: php artisan migrate\n";
    exit(1);
}

// 3. List all tenants with their active status
echo "3. Current tenants:\n";
$tenants = DB::connection($centralConn)->table('tenants')->select('id', 'active', 'created_at')->get();

if ($tenants->isEmpty()) {
    echo "   No tenants found.\n\n";
} else {
    foreach ($tenants as $tenant) {
        $status = $tenant->active ? 'ACTIVE' : 'INACTIVE';
        echo "   - {$tenant->id}: {$status} (active={$tenant->active})\n";
    }
    echo "\n";
}

// 4. Test toggle on first tenant
if ($tenants->isNotEmpty()) {
    $firstTenant = $tenants->first();
    echo "4. Testing toggle on tenant: {$firstTenant->id}\n";
    echo "   Current active value: {$firstTenant->active}\n";
    
    $newValue = !$firstTenant->active;
    echo "   Toggling to: " . ($newValue ? '1' : '0') . "\n";
    
    DB::connection($centralConn)
        ->table('tenants')
        ->where('id', $firstTenant->id)
        ->update(['active' => $newValue]);
    
    $updated = DB::connection($centralConn)
        ->table('tenants')
        ->where('id', $firstTenant->id)
        ->value('active');
    
    echo "   New value after update: {$updated}\n";
    echo "   Toggle " . ($updated == $newValue ? 'SUCCESS ✓' : 'FAILED ✗') . "\n\n";
    
    // Restore original value
    DB::connection($centralConn)
        ->table('tenants')
        ->where('id', $firstTenant->id)
        ->update(['active' => $firstTenant->active]);
    echo "   Restored original value: {$firstTenant->active}\n";
}

// 5. Check Tenant model's getCustomColumns
echo "\n5. Tenant model getCustomColumns: ";
echo json_encode(Tenant::getCustomColumns()) . "\n";
echo "   (should include 'active' to persist as real column)\n\n";

echo "=== Diagnostic Complete ===\n";
echo "\nIf all checks pass but toggle still doesn't work in the panel:\n";
echo "1. Check browser console for API errors\n";
echo "2. Check storage/logs/laravel.log for toggle logs\n";
echo "3. Clear frontend cache: npm run build\n";
echo "4. Clear backend cache: php artisan optimize:clear\n";
