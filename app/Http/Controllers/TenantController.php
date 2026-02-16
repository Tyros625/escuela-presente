<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantController extends AppBaseController
{
    /**
     * Tenancy identifies tenants by hostname only (no scheme, no port).
     * Normalize so "https://secundaria87.localhost:8000" → "secundaria87.localhost".
     */
    public static function normalizeDomainForTenancy(string $domain): string
    {
        $host = $domain;
        $host = preg_replace('#^https?://#i', '', $host);
        $host = preg_replace('#:\d+$#', '', $host);
        $host = trim($host, " \t\n\r\0\x0B/");

        return strtolower($host) ?: $domain;
    }

    public function index(Request $request)
    {
        // Read directly from DB to ensure we get the real 'active' column value,
        // not from the model's 'data' JSON which might be stale
        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        
        $tenants = DB::connection($centralConnection)
            ->table('tenants')
            ->select('*')
            ->when(Schema::hasColumn('tenants', 'active'), function ($query) {
                return $query->orderByRaw('CASE WHEN active = 1 THEN 0 ELSE 1 END');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($tenant) {
                // Cast active to boolean for frontend (MySQL returns 0/1, JS needs true/false)
                if (property_exists($tenant, 'active')) {
                    $tenant->active = (bool) $tenant->active;
                }
                return $tenant;
            });

        return $tenants;
    }

    public function show($id)
    {
        $tenant = Tenant::find($id);
        if (empty($tenant)) {
            return $this->sendError('Tenant not found', 404);
        }
        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        $row = DB::connection($centralConnection)->table('tenants')->where('id', $id)->first();
        $data = (array) $row;
        unset($data['password']);
        if (isset($data['active'])) {
            $data['active'] = (bool) $data['active'];
        }
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        if (empty($tenant)) {
            return $this->sendError('Tenant not found', 404);
        }
        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        $payload = $request->only(['active', 'access_start', 'access_end']);
        $allowed = [];
        if (array_key_exists('active', $payload)) {
            $allowed['active'] = (bool) $payload['active'];
        }
        if (array_key_exists('access_start', $payload)) {
            $allowed['access_start'] = $payload['access_start'] ?: null;
        }
        if (array_key_exists('access_end', $payload)) {
            $allowed['access_end'] = $payload['access_end'] ?: null;
        }
        if (empty($allowed)) {
            return $this->sendError('No valid fields to update', 400);
        }
        DB::connection($centralConnection)->table('tenants')->where('id', $id)->update($allowed);
        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado',
        ]);
    }

    public function store(RegisterTenantRequest $request)
    {
        $input = $request->validated();
        $domainNormalized = self::normalizeDomainForTenancy($input['domain']);
        $subdomain = explode('.', $domainNormalized)[0];

        $now = Carbon::now();
        $accessEnd = $now->copy()->addMonth();

        $tenant = Tenant::create([
            'id' => $subdomain,
            'school_name' => $input['school_name'],
            'cct' => $input['cct'],
            'domain' => $domainNormalized,
            'email' => $input['email'],
            'password' => $input['password'],
            'country_code' => $input['country_code'],
            'phone' => $input['phone'],
            'access_start' => $now->toDateString(),
            'access_end' => $accessEnd->toDateString(),
        ]);

        $tenant->createDomain(['domain' => $domainNormalized]);
        // $this->createWebhookConekta($input['domain']);

        return response()->json($tenant);
    }

    public function destroy($id)
    {
        $tenant = Tenant::find($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        $tenant->delete();

        return $this->sendSuccess('Tenant deleted successfully');
    }

    public function toggleActive($id)
    {
        $tenant = Tenant::find($id);

        if (empty($tenant)) {
            \Log::error('TenantController::toggleActive - Tenant not found', ['id' => $id]);
            return $this->sendError('Tenant not found');
        }

        if (! Schema::hasColumn($tenant->getTable(), 'active')) {
            \Log::error('TenantController::toggleActive - Active column missing', [
                'table' => $tenant->getTable(),
                'connection' => $tenant->getConnectionName(),
            ]);
            return $this->sendError('Active column not available. Run migrations.', 400);
        }

        $centralConnection = config('tenancy.database.central_connection', config('database.default'));
        \Log::info('TenantController::toggleActive - Reading current active', [
            'tenant_id' => $tenant->id,
            'connection' => $centralConnection,
        ]);

        $current = DB::connection($centralConnection)
            ->table('tenants')
            ->where('id', $tenant->id)
            ->value('active');
        $newActive = ! (bool) $current;

        \Log::info('TenantController::toggleActive - Updating active', [
            'tenant_id' => $tenant->id,
            'current' => $current,
            'newActive' => $newActive,
        ]);

        DB::connection($centralConnection)
            ->table('tenants')
            ->where('id', $tenant->id)
            ->update(['active' => $newActive]);

        \Log::info('TenantController::toggleActive - Toggle completed', [
            'tenant_id' => $tenant->id,
            'active' => $newActive,
        ]);

        return response()->json([
            'success' => true,
            'message' => $newActive ? 'Cliente activado' : 'Cliente desactivado',
            'active' => $newActive,
        ]);
    }

    private function createWebhookConekta($domain)
    {
        $token = base64_encode(env('CONEKTA_PRIVATE_KEY'));
        $data = [
            'url' => 'https://'.$domain.'/api/payments/webhook',
            'synchronous' => false,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.conekta.io/webhooks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.conekta-v2.0.0+json',
                'Authorization: Basic '.$token,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:'.$err;
        } else {
            return $response;
        }
    }
}
