<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;
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
        $query = Tenant::query();
        if (Schema::hasColumn((new Tenant)->getTable(), 'active')) {
            $query->orderByRaw('CASE WHEN active = 1 THEN 0 ELSE 1 END')->orderBy('created_at', 'desc');
        }
        return $query->get();
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

        $tenant->getDatabaseName();

        $tenant->delete();

        return $this->sendSuccess('Tenant deleted successfully');
    }

    public function toggleActive($id)
    {
        $tenant = Tenant::find($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        if (! Schema::hasColumn($tenant->getTable(), 'active')) {
            return $this->sendError('Active column not available', 400);
        }

        $tenant->active = ! $tenant->active;
        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => $tenant->active ? 'Cliente activado' : 'Cliente desactivado',
            'active' => (bool) $tenant->active,
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
