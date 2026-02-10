<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;
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
            ->get();

        return $tenants;
    }

    public function store(RegisterTenantRequest $request)
    {
        $input = $request->validated();
        $domainNormalized = self::normalizeDomainForTenancy($input['domain']);
        $subdomain = explode('.', $domainNormalized)[0];

        $tenant = Tenant::create([
            'id' => $subdomain,
            'school_name' => $input['school_name'],
            'cct' => $input['cct'],
            'domain' => $domainNormalized,
            'email' => $input['email'],
            'password' => $input['password'],
            'country_code' => $input['country_code'],
            'phone' => $input['phone'],
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
