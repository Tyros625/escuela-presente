<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        if (Schema::hasColumn($tenant->getTable(), 'active') && $tenant->active == false) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Esta institución está desactivada. Contacte al administrador.',
                ], 403);
            }

            abort(403, 'Esta institución está desactivada. Contacte al administrador.');
        }

        return $next($request);
    }
}
