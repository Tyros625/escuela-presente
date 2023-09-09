<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends AppBaseController
{
    public function index(Request $request)
    {
        return Tenant::all();
    }

    public function store(RegisterTenantRequest $request)
    {
        $input = $request->validated();
        $subdomain = explode('.', $input['domain'])[0];

        $tenant = Tenant::create([
            'id' => $subdomain,
            'school_name' => $input['school_name'],
            'cct' => $input['cct'],
            'domain' => $input['domain'],
            'email' => $input['email'],
            'password' => $input['password'],
            'country_code' => $input['country_code'],
            'phone' => $input['phone'],
        ]);

        $tenant->createDomain(['domain' => $input['domain']]);
        //$this->createWebhookConekta($input['domain']);

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
