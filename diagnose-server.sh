#!/bin/bash
# Server diagnostic script - run on server

echo "=== TENANT ACTIVE DIAGNOSTIC ==="
echo ""

echo "1. Checking if active column exists..."
php artisan tinker --execute="
\$columns = DB::select('DESCRIBE tenants');
foreach (\$columns as \$col) {
    if (\$col->Field === 'active') {
        echo 'FOUND: active column exists\n';
        echo 'Type: ' . \$col->Type . '\n';
        echo 'Default: ' . \$col->Default . '\n';
        break;
    }
}
"

echo ""
echo "2. Checking tenants data..."
php artisan tinker --execute="
\$tenants = DB::table('tenants')->select('id', 'active', 'created_at')->get();
echo 'Total tenants: ' . \$tenants->count() . '\n';
foreach (\$tenants as \$t) {
    echo \$t->id . ' => active: ' . \$t->active . '\n';
}
"

echo ""
echo "3. Testing toggle on first tenant..."
php artisan tinker --execute="
\$first = DB::table('tenants')->first();
if (\$first) {
    echo 'Testing on: ' . \$first->id . '\n';
    echo 'Current value: ' . \$first->active . '\n';
    
    \$new = !\$first->active;
    DB::table('tenants')->where('id', \$first->id)->update(['active' => \$new]);
    
    \$updated = DB::table('tenants')->where('id', \$first->id)->value('active');
    echo 'New value: ' . \$updated . '\n';
    echo 'Toggle ' . (\$updated == \$new ? 'SUCCESS' : 'FAILED') . '\n';
    
    // Restore
    DB::table('tenants')->where('id', \$first->id)->update(['active' => \$first->active]);
    echo 'Restored to: ' . \$first->active . '\n';
}
"

echo ""
echo "4. Checking route registration..."
php artisan route:list --path=api/tenants | grep toggle

echo ""
echo "5. Checking git commit..."
git log -1 --oneline

echo ""
echo "=== DIAGNOSTIC COMPLETE ==="
