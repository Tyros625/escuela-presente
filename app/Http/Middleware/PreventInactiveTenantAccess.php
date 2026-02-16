<?php

namespace App\Http\Middleware;

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

        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        $table = 'tenants';

        if (! Schema::connection($centralConnection)->hasColumn($table, 'active')) {
            \Log::warning('PreventInactiveTenantAccess - active column not found in central DB, skipping check');
            return $next($request);
        }

        $row = DB::connection($centralConnection)
            ->table($table)
            ->where('id', $tenant->id)
            ->first();

        if (! $row) {
            return $next($request);
        }

        $isActive = isset($row->active) && $row->active !== false && $row->active !== 0;
        $hasAccessColumns = Schema::connection($centralConnection)->hasColumn($table, 'access_start')
            && Schema::connection($centralConnection)->hasColumn($table, 'access_end');
        $today = now()->toDateString();
        $accessStart = $hasAccessColumns && isset($row->access_start) ? $row->access_start : null;
        $accessEnd = $hasAccessColumns && isset($row->access_end) ? $row->access_end : null;

        $blocked = false;
        $reason = null;

        if (! $isActive) {
            $blocked = true;
            $reason = 'blocked';
        } elseif ($accessStart !== null && $today < $accessStart) {
            $blocked = true;
            $reason = 'not_started';
        } elseif ($accessEnd !== null && $today > $accessEnd) {
            $blocked = true;
            $reason = 'expired';
        }

        if ($blocked) {
            \Log::warning('PreventInactiveTenantAccess - blocking tenant access', [
                'tenant_id' => $tenant->id,
                'reason' => $reason,
            ]);

            $whatsappUrl = config('app.admin_whatsapp_url');
            if (! $whatsappUrl && ! empty($tenant->country_code) && ! empty($tenant->phone)) {
                $number = preg_replace('/\D/', '', (string) $tenant->phone);
                $whatsappUrl = 'https://wa.me/' . ((int) $tenant->country_code) . $number;
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                $message = $reason === 'expired'
                    ? 'Su período de acceso ha finalizado. Contacte al administrador por WhatsApp.'
                    : ($reason === 'blocked'
                        ? 'El acceso a esta institución está desactivado. Contacte al administrador.'
                        : 'El acceso aún no está disponible en las fechas indicadas.');
                return response()->json(['message' => $message], 403);
            }

            return response()->view('tenant-disabled', [
                'reason' => $reason,
                'whatsappUrl' => $whatsappUrl,
            ], 403);
        }

        return $next($request);
    }
}
