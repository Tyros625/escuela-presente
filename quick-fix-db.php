<?php
/**
 * Quick DB diagnostic and fix
 * Run on server: php quick-fix-db.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== QUICK DB DIAGNOSTIC ===\n\n";

// 1. Check connection
echo "1. Database connection\n";
try {
    $dbName = DB::connection()->getDatabaseName();
    echo "   Connected to: {$dbName}\n";
    echo "   Driver: " . DB::connection()->getDriverName() . "\n\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Check if tenants table exists
echo "2. Tenants table\n";
if (!Schema::hasTable('tenants')) {
    echo "   ERROR: tenants table does not exist!\n\n";
    exit(1);
}
echo "   ✓ tenants table exists\n\n";

// 3. Check columns
echo "3. Table structure\n";
$columns = DB::select('DESCRIBE tenants');
$hasActive = false;
foreach ($columns as $col) {
    if ($col->Field === 'active') {
        $hasActive = true;
        echo "   ✓ active column found\n";
        echo "     Type: {$col->Type}\n";
        echo "     Null: {$col->Null}\n";
        echo "     Default: {$col->Default}\n";
        break;
    }
}

if (!$hasActive) {
    echo "   ✗ active column NOT FOUND\n";
    echo "   Run: php artisan migrate --force\n\n";
    exit(1);
}
echo "\n";

// 4. List tenants
echo "4. Current tenants\n";
$tenants = DB::table('tenants')->get();
if ($tenants->isEmpty()) {
    echo "   No tenants found\n\n";
} else {
    echo "   Total: {$tenants->count()}\n";
    foreach ($tenants as $t) {
        $active = isset($t->active) ? $t->active : 'NULL';
        $status = $active ? 'ACTIVE' : 'INACTIVE';
        echo "   - {$t->id}: {$status} (value: {$active})\n";
    }
    echo "\n";
}

// 5. Check if data column has active inside it
if ($tenants->isNotEmpty()) {
    echo "5. Checking if 'active' is mistakenly in 'data' JSON\n";
    $first = $tenants->first();
    if (isset($first->data)) {
        $data = json_decode($first->data, true);
        if (isset($data['active'])) {
            echo "   ⚠️  WARNING: 'active' found in data JSON!\n";
            echo "      Value in data: " . $data['active'] . "\n";
            echo "      This means the model was saving to data, not the column.\n";
            echo "      The code has been fixed to use DB directly.\n\n";
            
            // Migrate data to column
            echo "   Fixing: Moving 'active' from data to column for all tenants...\n";
            foreach ($tenants as $tenant) {
                $tData = json_decode($tenant->data, true);
                if (isset($tData['active'])) {
                    $activeValue = $tData['active'];
                    unset($tData['active']);
                    
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update([
                            'active' => $activeValue,
                            'data' => json_encode($tData)
                        ]);
                    echo "     Fixed: {$tenant->id} => active={$activeValue}\n";
                }
            }
            echo "   ✓ Migration complete\n\n";
        } else {
            echo "   ✓ 'active' not in data JSON (good)\n\n";
        }
    }
}

// 6. Verify
echo "6. Final verification\n";
$final = DB::table('tenants')->select('id', 'active')->get();
foreach ($final as $t) {
    echo "   {$t->id}: active={$t->active}\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Test toggle in admin panel\n";
echo "2. Watch logs: tail -f storage/logs/laravel.log\n";
echo "3. Try accessing an inactive tenant domain\n";
