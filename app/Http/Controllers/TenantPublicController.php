<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Jobs\CreateTenantPublic;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantPublicController extends AppBaseController
{
    public function index(Request $request)
    {
        return Tenant::all();
    }

    public function store(RegisterTenantRequest $request)
    {
        $input = $request->validated();
        $subdomain = explode('.', $input['domain'])[0];

        CreateTenantPublic::dispatch($input, $subdomain);

        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'Tenant saved successfully',
        ]);
    }
}
