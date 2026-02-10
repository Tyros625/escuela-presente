<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Mail\EmailForQueue;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NewTenantRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

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

        // Send welcome email synchronously so it runs regardless of queue worker
        $emailData = array_merge($input, ['domain' => $domainNormalized]);
        try {
            Mail::to($input['email'])->send(new EmailForQueue($emailData));
            Log::info('TenantPublicController: Welcome email sent.', ['email' => $input['email'], 'domain' => $domainNormalized]);
        } catch (\Throwable $e) {
            Log::warning('TenantPublicController: Failed to send tenant welcome email.', [
                'email' => $input['email'],
                'domain' => $domainNormalized,
                'exception' => $e->getMessage(),
            ]);
        }

        // Notify admin users only if users table has is_admin column
        try {
            if (Schema::hasColumn((new User)->getTable(), 'is_admin')) {
                $adminUsers = User::where('is_admin', true)->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new NewTenantRegisteredNotification($tenant));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TenantPublicController: Failed to notify admins.', [
                'exception' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $tenant,
            'message' => 'Tenant saved successfully',
        ]);
    }
}
