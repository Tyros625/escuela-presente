<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Mail\EmailForQueue;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NewTenantRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TenantPublicController extends AppBaseController
{
    public function index(Request $request)
    {
        return Tenant::all();
    }

    public function store(RegisterTenantRequest $request)
    {
        $input = $request->validated();
        $domainNormalized = TenantController::normalizeDomainForTenancy($input['domain']);
        $subdomain = explode('.', $domainNormalized)[0];

        // Create tenant immediately (like admin panel)
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

        // Send email with domain link
        $emailData = array_merge($input, ['domain' => $domainNormalized]);
        $email = new EmailForQueue($emailData);
        Mail::to($input['email'])->send($email);

        // Notify all admin users
        $adminUsers = User::where('is_admin', true)->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new NewTenantRegisteredNotification($tenant));
        }

        return response()->json([
            'success' => true,
            'data' => $tenant,
            'message' => 'Tenant saved successfully',
        ]);
    }
}
