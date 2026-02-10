<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class PreventInactiveTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if (! $tenant) {
            return $next($request);
        }

        if (! Schema::hasColumn((new Tenant)->getTable(), 'active')) {
            \Log::warning('PreventInactiveTenantAccess - active column not found, skipping check');
            return $next($request);
        }

        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        // Read active from DB to ensure we have current value (tenant() may be cached)
        $isActive = DB::connection($centralConnection)
            ->table('tenants')
            ->where('id', $tenant->id)
            ->value('active');

        \Log::info('PreventInactiveTenantAccess - checking tenant access', [
            'tenant_id' => $tenant->id,
            'isActive' => $isActive,
            'connection' => $centralConnection,
            'url' => $request->url(),
        ]);

        if ($isActive === false || $isActive === 0) {
            \Log::warning('PreventInactiveTenantAccess - blocking inactive tenant', [
                'tenant_id' => $tenant->id,
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'El acceso a esta institución está desactivado. Contacte al administrador.',
                ], 403);
            }

            return response()->view('tenant-disabled', [], 403);
        }

        return $next($request);
    }
}
